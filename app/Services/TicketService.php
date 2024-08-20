<?php

namespace App\Services;

use App\Models\NCCallType;
use App\Models\NCServiceResponsibleGroup;
use App\Models\NCTicket;
use App\Models\NCTicketTimeline;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        ['value' => 'NEW', 'label' => 'NEW'],
        ['value' => 'OPEN', 'label' => 'OPEN'],
        ['value' => 'PENDING', 'label' => 'PENDING'],
        ['value' => 'CLOSED', 'label' => 'CLOSED'],
        ['value' => 'REOPEN', 'label' => 'REOPEN'],
    ];

    protected $ticketUuid;

    public function __construct(ServiceTypeConfigService $serviceTypeConfigService, NotificationService $notificationService, GroupService $groupService)
    {
        $this->ticketUuid = generateTicketUuid();
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
        $inputRequiredFields = $validated['requiredField'] ?? [];

        $ticketRelated = $this->generateRequiredFieldsAndTicketData($inputRequiredFields);

        $requiredFieldsNew = $this->prepareRequiredFields($ticketRelated['requiredFields']);

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

        $authUserId = Auth::id();
        $escalation = $this->prepareTicketEscalation($validated['callTypeId'], $serviceTypeConfigs->is_escalation ?? 'NO');
        $status = $escalation === 'yes' ? 'NEW' : 'CLOSED';

        $ticketsData = [];
        foreach (range(0, $ticketRelated['totalTickets'] - 1) as $i) {

            $ticketAttachments = uploadMediaGetPath($validated['attachment'], 'attachments') ?? null;

            $ticketInfo = [
                'uuid' => generateTicketUuid(), // Uuid::uuid4()->toString(),
                'call_type_id' => $validated['callTypeId'],
                'call_category_id' => $validated['callCategoryId'],
                'call_sub_category_id' => $validated['callSubCategoryId'],
                'caller_mobile_no' => $validated['callerMobileNo'],
                'required_fields' => json_encode($ticketRelated),
                'responsible_group_ids' => $responsibleGroupIdsStr,
                'is_ticket_reassign' => 0,
                'comments' => $validated['comments'],
                'sla_status' => 'NORMAL',
                'attachment' => $ticketAttachments,
                'ticket_notification_status' => 1,
                'is_customer_notified' => 0,
                'ticket_created_at' => Carbon::now(),
                'ticket_status' => $status,
                'ticket_channel' => 'PANEL',
                'ticket_created_by' => $authUserId,
                'ticket_updated_by' => $authUserId,
            ];

            $ticket = NCTicket::create($ticketInfo);
            $ticketId = $ticket->id;
            $ticketUuid = $ticket->uuid;

            // Store ticket ID and UUID
            /* $ticketsData[] = [
            'ticketId' => $ticketId,
            'ticketUuid' => $ticketUuid,
            ]; */

            $ticketsData[] = $ticketId;

            $this->bulkInsertRequiredFields($ticketRelated['requiredFields'][$i], $ticketId, $authUserId);

            $data = [
                'ticket_id' => $ticketId,
                'responsible_group_ids' => $responsibleGroupIdsStr,
                'ticket_status' => $status,
                'ticket_comments' => $validated['comments'],
                'ticket_attachments' => $ticketAttachments,
                'ticket_opened_by' => null,
                'ticket_status_updated_by' => $authUserId,
                'opened_at' => $ticket->ticket_created_at,
                'last_time_opened_at' => $ticket->ticket_created_at,
            ];

            $this->createTicketTimeline($data);

            // Send notification for each ticket
            $this->notificationService->sendTicketNotification($ticket, $serviceTypeConfigs, $responsibleGroupIdsStr);
        }

        if ($ticketsData) {
            // Handle notifications for responsible groups (if needed)
            $groupName = $this->responsibleGroupInfo($responsibleGroupIdsStr);
            return [
                'code' => Response::HTTP_CREATED,
                'status' => 'success',
                'message' => 'Tickets have been created. Responsible groups: ' . $groupName,
                'data' => [
                    'ticketId' => json_encode($ticketsData),
                ],
            ];
        }

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'failed',
            'message' => 'Something went wrong creating tickets',
        ];
    }

    public function createTicketTimeline($data)
    {
        return NCTicketTimeline::create($data);
    }

    protected function bulkInsertRequiredFields(array $requiredFields, int $ticketId, int $userId)
    {
        // Prepare data for bulk insert
        $insertData = array_map(function ($value, $key) use ($ticketId, $userId) {
            return [
                'ticket_id' => $ticketId,
                'required_field_id' => (int) $key,
                'required_field_value' => $value,
                'created_by' => $userId,
                'updated_by' => $userId,
                'last_updated_by' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $requiredFields, array_keys($requiredFields));

        // Perform the bulk insert
        DB::table('tickets_required_fields')->insert($insertData);
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
                'data' => [],
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
        $data = [];
        foreach ($requiredFields as $key => $fields) {
            foreach ($fields as $id => $value) {
                $data[$key][] = [
                    'required_field_id' => $id,
                    'required_field_value' => $value,
                ];
            }

        }

        return $data;
    }

    protected function generateRequiredFieldsAndTicketData($dataArr)
    {
        $totalTickets = max(array_map('count', $dataArr));
        $requiredFieldsAndValue = [];

        for ($i = 0; $i < $totalTickets; $i++) {
            $ticket = [];
            foreach ($dataArr as $fieldId => $values) {
                if (isset($values[$i + 1])) {
                    $ticket[$fieldId] = $values[$i + 1];
                } else {
                    $ticket[$fieldId] = null;
                }
            }
            $requiredFieldsAndValue[] = $ticket;
        }

        return [
            'totalTickets' => $totalTickets,
            'requiredFields' => $requiredFieldsAndValue,
        ];

    }

    protected function prepareTicketData(array $params): array
    {
        $validated = $params['validated'];
        $requiredFields = $params['requiredFields'];
        $responsibleGroupIdsStr = $params['responsibleGroups'];
        $serviceTypeConfigs = $params['serviceTypeConfigs'];
        $authUserId = Auth::id();

        $escalation = $this->prepareTicketEscalation($validated['callTypeId'], $serviceTypeConfigs->is_escalation ?? 'NO');
        $status = $escalation === 'yes' ? 'OPEN' : 'CLOSED';

        return [
            'uuid' => generateTicketUuid(), // Uuid::uuid4()->toString(),
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
