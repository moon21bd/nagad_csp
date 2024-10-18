<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Group;
use App\Models\NCCallType;
use App\Models\NCServiceResponsibleGroup;
use App\Models\NCTicket;
use App\Models\NCTicketTimeline;
use App\Models\TicketComment;
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
        ['value' => 'CREATED', 'label' => 'CREATED'],
        ['value' => 'OPENED', 'label' => 'OPENED'],
        ['value' => 'RESOLVED', 'label' => 'RESOLVED'],
        ['value' => 'ASSIGNED', 'label' => 'ASSIGNED'],
        // ['value' => 'PENDING', 'label' => 'PENDING'],
        ['value' => 'CLOSED', 'label' => 'CLOSED'],
        ['value' => 'CLOSED - REACHED', 'label' => 'CLOSED - REACHED'],
        ['value' => 'CLOSED - NOT RECEIVED', 'label' => 'CLOSED - NOT RECEIVED'],
        ['value' => 'CLOSED - NOT CONNECTED', 'label' => 'CLOSED - NOT CONNECTED'],
        ['value' => 'CLOSED - SWITCHED OFF', 'label' => 'CLOSED - SWITCHED OFF'],
        ['value' => 'CLOSED - NOT COOPERATED', 'label' => 'CLOSED - NOT COOPERATED'],
        ['value' => 'REOPEN', 'label' => 'REOPEN'],
    ];

    // Ticket sources arr
    protected $sources = [
        ['value' => 'NAGAD-SEBA', 'label' => 'Nagad Seba'],
        ['value' => 'CALL-CENTER', 'label' => 'Call Center'],
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

    public function getAllTickets(array $filters = [])
    {
        $user = auth()->user();
        $query = NCTicket::with(['creator:id,name', 'callType', 'callCategory', 'callSubCategory', 'attachments', 'comments']);

        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->whereIn('ticket_status', $filters['status']);
        }

        if (isset($filters['groups']) && !empty($filters['groups'])) {
            $query->whereIn('assign_to_group_id', $filters['groups']);
        }

        if (isset($filters['my_tickets']) && !empty($filters['my_tickets'])) {
            $query->where('assign_to_user_id', $filters['my_tickets']);
        }

        if (isset($filters['created_by']) && !empty($filters['created_by'])) {
            $query->where('ticket_created_by', $filters['created_by']);
        }

        if (isset($filters['ticket_source']) && !empty($filters['ticket_source'])) {
            $query->where('ticket_source', $filters['ticket_source']);
        }

        if (isset($filters['service_category']) && $filters['service_category'] !== '') {
            $query->where('call_category_id', $filters['service_category']);
        }

        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            $tickets = $query->get();
        } elseif ($user->hasRole('owner') && $user->group->hasOwner()) {
            $tickets = $query->where('assign_to_group_id', $user->group_id)->get();
        } else {
            $tickets = $query->where('assign_to_group_id', $user->group_id)->get();
        }

        return $this->addTatHoursToTickets($tickets);

        // return $tickets;
    }

    public function createTicket(array $validated)
    {

        $ticketCount = 1; // by default ticket

        if (!empty($validated['requiredField'])) {
            $inputRequiredFields = $validated['requiredField'] ?? [];
            $ticketRelated = $this->generateRequiredFieldsAndTicketData($inputRequiredFields);
            $ticketCount = $ticketRelated['totalTickets'];

            $configResult = $this->getTransactionIdConfig($validated);

            if ($configResult) {
                $transactionConfigId = $configResult->id;

                foreach ($ticketRelated['requiredFields'] as $i => $requiredFields) {
                    if (isset($requiredFields[$transactionConfigId])) {
                        $transactionId = $requiredFields[$transactionConfigId];

                        if ($this->checkTransactionIdExists($validated, $transactionId, $transactionConfigId)) {
                            return [
                                'code' => Response::HTTP_CONFLICT,
                                'status' => 'error',
                                'message' => "Transaction ID ($transactionId) already exists. Ticket creation aborted.",
                            ];
                        }
                    }
                }
            }

        }

        // Fetch service type configurations and responsible group IDs
        $serviceTypeConfigs = $this->showServiceTypeConfig(
            $validated['callTypeId'],
            $validated['callCategoryId'],
            $validated['callSubCategoryId']
        );

        $responsibleGroupIdsStr = $responsibleGroupIds = $this->getResponsibleGroupIds([
            'call_type_id' => $validated['callTypeId'],
            'call_category_id' => $validated['callCategoryId'],
            'call_sub_category_id' => $validated['callSubCategoryId'],
        ])->implode(',');

        $authUserId = Auth::id();
        $escalation = $this->prepareTicketEscalation($validated['callTypeId'], $serviceTypeConfigs->is_escalation ?? 'NO');
        $status = $escalation === 'yes' ? 'CREATED' : 'CLOSED';

        $authUserGroupId = Auth::user()->group_id;
        $ticketSource = $authUserGroupId === 3 ? "NAGAD-SEBA" : ($authUserGroupId === 1 ? 'NAGAD-CC-AGENT' : 'PANEL');

        $ticketsData = [];
        foreach (range(0, $ticketCount - 1) as $i) {

            $ticketInfo = [
                'uuid' => generateTicketUuid(), // Uuid::uuid4()->toString(),
                'call_type_id' => $validated['callTypeId'],
                'call_category_id' => $validated['callCategoryId'],
                'call_sub_category_id' => $validated['callSubCategoryId'],
                'caller_mobile_no' => $validated['callerMobileNo'],
                'required_fields' => !empty($ticketRelated) ? json_encode($ticketRelated) : '{}',
                'responsible_group_ids' => $responsibleGroupIdsStr,
                'is_ticket_reassign' => 0,
                // 'comments' => $validated['comments'],
                'sla_status' => 'NORMAL',
                'ticket_notification_status' => 1,
                'is_customer_notified' => 0,
                'ticket_created_at' => Carbon::now(),
                'ticket_status' => $status,
                'ticket_source' => $ticketSource,
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

            // $ticketsData[] = $ticketId;
            $ticketsData[] = $ticketUuid;

            if (!empty($ticketRelated['requiredFields'])) {
                $this->bulkInsertRequiredFields($ticketRelated['requiredFields'][$i], $ticketId, $authUserId, [
                    'call_type_id' => $validated['callTypeId'],
                    'call_category_id' => $validated['callCategoryId'],
                    'call_sub_category_id' => $validated['callSubCategoryId'],
                ]);
            }

            $data = [
                'ticket_id' => $ticketId,
                'responsible_group_ids' => $responsibleGroupIdsStr,
                'ticket_status' => $status,
                // 'ticket_comments' => $validated['comments'],
                // 'ticket_attachments' => $ticketAttachments,
                'ticket_opened_by' => null,
                'ticket_status_updated_by' => $authUserId,
                'opened_at' => $ticket->ticket_created_at,
                'last_time_opened_at' => $ticket->ticket_created_at,
            ];

            $this->createTicketTimeline($data);
            if (!empty($validated['attachment'])) {
                $ticketAttachments = uploadAttachment($validated['attachment'], $validated['attachmentType']) ?? null;
                Attachment::create([
                    'ticket_id' => $ticketId,
                    'path' => $ticketAttachments,
                    'created_by' => $authUserId,
                ]);
            }

            if (!empty($validated['comments'])) {
                TicketComment::create([
                    'ticket_id' => $ticketId,
                    'comment' => $validated['comments'],
                    'created_by' => $authUserId,
                ]);
            }

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
                    'ticketId' => implode(', ', $ticketsData),
                ],
            ];
        }

        return [
            'code' => Response::HTTP_EXPECTATION_FAILED,
            'status' => 'failed',
            'message' => 'Something went wrong creating tickets',
        ];
    }

    /* public function createTicketTimeline($data)
    {
    return NCTicketTimeline::create($data);
    } */

    public function createTicketTimeline($data)
    {
        $existingOpenedStatus = NCTicketTimeline::where('ticket_id', $data['ticket_id'])
            ->where('ticket_status', 'OPENED')
            ->exists();

        if ($data['ticket_status'] === 'OPENED' && $existingOpenedStatus) {
            Log::debug('Ticket (ID:' . $data['ticket_id'] . ') is already in "OPENED" status.');
            return false;
            // throw new \Exception('Ticket is already in "OPENED" status.');
        }

        return NCTicketTimeline::create($data);
    }

    protected function bulkInsertRequiredFields(array $requiredFields, int $ticketId, int $userId, array $wrappedUp)
    {
        // Prepare data for bulk insert
        $insertData = array_map(function ($value, $key) use ($ticketId, $userId, $wrappedUp) {

            return [
                'ticket_id' => $ticketId,
                'call_type_id' => $wrappedUp['call_type_id'],
                'call_category_id' => $wrappedUp['call_category_id'],
                'call_sub_category_id' => $wrappedUp['call_sub_category_id'],
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

    protected function getTransactionIdConfig(array $validated)
    {
        $wrappedUp = [
            'call_type_id' => $validated['callTypeId'],
            'call_category_id' => $validated['callCategoryId'],
            'call_sub_category_id' => $validated['callSubCategoryId'],
        ];

        return DB::table('nc_required_field_configs')
            ->where('input_field_name', 'LIKE', '%Transaction id%')
            ->where($wrappedUp)
            ->first();
    }

    protected function checkTransactionIdExists(array $validated, $transactionId, $configId)
    {
        $wrappedUp = [
            'call_type_id' => $validated['callTypeId'],
            'call_category_id' => $validated['callCategoryId'],
            'call_sub_category_id' => $validated['callSubCategoryId'],
        ];

        return DB::table('tickets_required_fields')
            ->where('required_field_value', $transactionId)
            ->where($wrappedUp)
            ->where('required_field_id', $configId)
            ->exists();
    }

    public function getStatuses()
    {
        return $this->statuses;
    }

    public function getSources()
    {
        return $this->sources;
    }

    public function updateTicket(Request $request, $id)
    {
        $validated = $this->validateUpdateRequest($request);

        $ticketArr = $this->prepareUpdateDataArray($validated);

        try {
            $ticket = NCTicket::findOrFail($id);
            $ticket->update($ticketArr);
            $ticketId = $ticket->id;
            $ticketStatus = $ticket->ticket_status;

            // Update SLA status based on the ticket's updated status
            $this->updateSlaStatus($ticket);

            $data = [
                'ticket_id' => $ticketId,
                'responsible_group_ids' => $ticket->responsible_group_ids,
                'ticket_status' => $ticketStatus,
                // 'ticket_comments' => json_encode($validated['comments']),
                // 'ticket_attachments' => $ticket->attachment,
                'ticket_opened_by' => $ticket->ticket_updated_by,
                'ticket_status_updated_by' => $ticket->ticket_updated_by,
                'opened_at' => Carbon::now(),
                'last_time_opened_at' => Carbon::now(),
            ];
            $this->createTicketTimeline($data);

            foreach ($validated['comments'] ?? [] as $key => $comment) {
                $comment = TicketComment::create([
                    'ticket_id' => $ticketId,
                    'comment' => $comment['text'],
                    'created_by' => Auth::id(),
                ]);
            }

            return [
                'code' => Response::HTTP_OK,
                'status' => 'success',
                'message' => 'Ticket Updated.',
                'data' => [
                    'ticket_id' => $ticketId,
                    'status' => $ticketStatus,
                ],
            ];
        } catch (Exception $e) {
            Log::error('TICKET-UPDATE-FAILED: ' . $e->getLine() . "|" . $e->getMessage());
            return [
                'code' => Response::HTTP_BAD_REQUEST,
                'status' => 'failed',
                'message' => 'Ticket Update Failed.',
                'data' => [],
            ];
        }
    }

    public function updateSlaStatus($ticket)
    {
        $ticketStatus = $ticket->ticket_status;
        $currentTime = Carbon::now();

        switch ($ticketStatus) {
            case 'CLOSED':
            case 'CLOSED - REACHED':
            case 'CLOSED - NOT RECEIVED':
            case 'CLOSED - NOT CONNECTED':
            case 'CLOSED - SWITCHED OFF':
            case 'CLOSED - NOT COOPERATED':
                $group = NCServiceResponsibleGroup::where('group_id', $ticket->assign_to_group_id)->first();

                if ($group) {
                    $slaDeadline = Carbon::parse($ticket->sla_updated_at ?? $ticket->ticket_created_at)
                        ->addHours($group->tat_hours);
                    $ticket->sla_status = $currentTime->lessThanOrEqualTo($slaDeadline) ? 'met' : 'breached';
                }
                break;

            case 'REOPEN':
            case 'CREATED':
            case 'OPENED':
                $ticket->sla_status = 'in_progress';
                break;

            default:
                $ticket->sla_status = 'unknown';
                break;
        }

        if (in_array($ticketStatus, ['CLOSED', 'REOPEN', 'CREATED', 'OPENED'])) {
            $ticket->sla_updated_at = $currentTime;
        }

        $ticket->save();
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
            // 'comments' => $validated['comments'],
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
        $status = $escalation === 'yes' ? 'CREATED' : 'CLOSED';

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
            // 'attachment' => uploadMediaGetPath($validated['attachment'], 'attachments') ?? null,
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

    protected function addTatHoursToTickets($tickets)
    {
        foreach ($tickets as $ticket) {
            $responsibleGroup = NCServiceResponsibleGroup::where('call_type_id', $ticket->call_type_id)
                ->where('call_category_id', $ticket->call_category_id)
                ->where('call_sub_category_id', $ticket->call_sub_category_id)
                ->first();

            if ($responsibleGroup) {
                $ticket->tat_hours = $responsibleGroup->tat_hours;
            } else {
                $ticket->tat_hours = 0;
            }
        }

        return $tickets;
    }

}
