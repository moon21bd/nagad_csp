<?php

namespace App\Services;

use App\Models\NCCallType;
use App\Models\NCServiceResponsibleGroup;
use App\Models\NCTicket;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class TicketService
{
    protected $serviceTypeConfigService;
    protected $notificationService;
    protected $groupService;

    protected $authUserId;

    // Ticket statuses arr
    protected $statuses = [
        ['value' => 'OPEN', 'label' => 'OPEN'],
        ['value' => 'PENDING', 'label' => 'PENDING'],
        ['value' => 'CLOSED', 'label' => 'CLOSED'],
        ['value' => 'REOPEN', 'label' => 'REOPEN'],
    ];

    public function __construct(ServiceTypeConfigService $serviceTypeConfigService, NotificationService $notificationService, GroupService $groupService)
    {
        $this->serviceTypeConfigService = $serviceTypeConfigService;
        $this->notificationService = $notificationService;
        $this->groupService = $groupService;
        $this->authUserId = Auth::id();
    }

    public function getAllTickets()
    {
        return NCTicket::with(['callType', 'callCategory', 'callSubCategory'])
            ->get();
    }

    public function createTicket(array $validated)
    {
        $requiredFields = $this->prepareRequiredFields($validated['requiredField'] ?? []);

        // Fetch service type configurations and responsible group IDs
        $serviceTypeConfigs = $this->showServiceTypeConfig(
            $validated['callTypeId'],
            $validated['callCategoryId'],
            $validated['callSubCategoryId']
        );

        $responsibleGroupIds = $this->getResponsibleGroupIds([
            'call_type_id' => $validated['callTypeId'],
            'call_category_id' => $validated['callCategoryId'],
            'call_sub_category_id' => $validated['callSubCategoryId'],
        ]);

        $responsibleGroupIdsStr = $responsibleGroupIds->implode(',');

        $ticketData = $this->prepareTicketData([
            'validated' => $validated,
            'requiredFields' => $requiredFields,
            'responsibleGroups' => $responsibleGroupIdsStr,
            'serviceTypeConfigs' => $serviceTypeConfigs,
        ]);

        $ticket = NCTicket::create($ticketData);

        if ($ticket) {
            // Send notification
            $groupName = $this->responsibleGroupInfo($responsibleGroupIdsStr);
            $this->notificationService->sendTicketNotification($ticket, $serviceTypeConfigs, $responsibleGroupIdsStr);
            return [
                'code' => Response::HTTP_CREATED,
                'status' => 'success',
                'message' => 'A ticket has been created. Responsible groups: ' . $groupName,
                'data' => [
                    'ticketId' => $ticket->id,
                    'ticketUuid' => $ticket->uuid,
                ],
            ];
        }

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'failed',
            'message' => 'Something went wrong creating ticket',
        ];
    }

    public function getStatuses()
    {
        return $this->statuses;
    }

    public function updateTicket(Request $request, $id)
    {
        $validated = $this->validateUpdateRequest($request);

        $ticketArr = $this->prepareUpdateDataArray($validated);

        try {
            $ticket = NCTicket::findOrFail($id);
            $ticket->update($ticketArr);

            return [
                'code' => Response::HTTP_OK,
                'status' => 'success',
                'message' => 'Ticket Updated.',
                'data' => $ticket,
            ];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return [
                'code' => Response::HTTP_BAD_REQUEST,
                'status' => 'failed',
                'message' => 'Ticket Update Failed.',
                'data' => $ticket,
            ];
        }
    }

    protected function handleQueryException(QueryException $e)
    {
        if ($e->getCode() == 23000) { // SQLSTATE[23000] for integrity constraint violation
            return response()->json([
                'message' => 'Duplicate entry detected for the given combination of call type, call category, and call sub-category.',
            ], 400);
        }

        // Handle other types of query exceptions
        return response()->json([
            'message' => 'An error occurred while processing your request.',
            'error' => $e->getMessage(),
        ], 500);
    }

    protected function validateUpdateRequest(Request $request)
    {
        return $request->validate([
            'selectedStatus' => 'required|string',
            'comments' => 'nullable|array',
        ]);
    }

    protected function prepareUpdateDataArray(array $validated)
    {
        $dataArr = [
            'ticket_status' => $validated['selectedStatus'],
            'comments' => $validated['comments'],
            'updated_by' => Auth::id(),
            'ticket_updated_at' => Carbon::now(),
            'ticket_updated_by' => Auth::id(),
            'ticket_last_updated_by' => Auth::id(),
            'last_updated_by' => Auth::id(),
        ];

        return $dataArr;
    }

    protected function prepareRequiredFields(array $requiredFields): array
    {
        return array_map(function ($item, $value) {
            list($id, $field) = explode('|', $item);
            return [
                'id' => (int) $id,
                'field' => $field,
                'value' => $value,
            ];
        }, array_keys($requiredFields), $requiredFields);
    }

    protected function prepareTicketData(array $params): array
    {
        $validated = $params['validated'];
        $requiredFields = $params['requiredFields'];
        $responsibleGroupIdsStr = $params['responsibleGroups'];
        $serviceTypeConfigs = $params['serviceTypeConfigs'];
        $authUserId = Auth::id();
        $now = Carbon::now();

        $escalation = $this->prepareTicketEscalation($validated['callTypeId'], $serviceTypeConfigs->is_escalation ?? 'NO');
        $status = $escalation === 'yes' ? 'OPEN' : 'CLOSED';

        return [
            'uuid' => Uuid::uuid4()->toString(),
            'call_type_id' => $validated['callTypeId'],
            'call_category_id' => $validated['callCategoryId'],
            'call_sub_category_id' => $validated['callSubCategoryId'],
            'caller_mobile_no' => $validated['callerMobileNo'],
            'required_fields' => json_encode($requiredFields),
            'responsible_group_ids' => $responsibleGroupIdsStr,
            'is_ticket_reassign' => 0,
            'comments' => $validated['comments'],
            'sla_status' => 'NORMAL',
            'attachment' => uploadMediaGetPath($validated['attachment'], 'attachments') ?? null,
            'ticket_notification_status' => 1,
            'is_customer_notified' => 0,
            'ticket_status' => $status,
            'ticket_channel' => 'PANEL',
            'ticket_created_by' => $authUserId,
            'ticket_updated_by' => $authUserId,
        ];
    }

    protected function getResponsibleGroupIds(array $params)
    {
        return NCServiceResponsibleGroup::where($params)->pluck('group_id');
    }

    protected function prepareTicketEscalation($callTypeId, $isEscalation)
    {
        $callType = NCCallType::find($callTypeId);

        if ($callType && strtoupper($callType->call_type_name) === 'QUERY') {
            return 'no';
        }

        return $isEscalation;
    }

    protected function showServiceTypeConfig($callTypeId, $callCategoryId, $callSubCategoryId)
    {
        try {
            return $this->serviceTypeConfigService->getServiceTypeConfigs($callTypeId, $callCategoryId, $callSubCategoryId);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }
    protected function responsibleGroupInfo($groupIds)
    {
        $groupIdsArray = explode(',', $groupIds);
        return $this->groupService->getGroupName($groupIdsArray);
    }

}