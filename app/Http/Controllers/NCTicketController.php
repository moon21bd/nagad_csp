<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\NCTicket;
use App\Models\TicketsRequiredField;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\ServiceTypeConfigService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NCTicketController extends Controller
{
    protected $ticketService;
    protected $notificationService;
    protected $serviceTypeConfig;

    public function __construct(TicketService $ticketService, NotificationService $notificationService, ServiceTypeConfigService $serviceTypeConfig)
    {

        $this->notificationService = $notificationService;
        $this->ticketService = $ticketService;
        $this->serviceTypeConfig = $serviceTypeConfig;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = $this->ticketService->getAllTickets();
        return response()->json($tickets);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'callTypeId' => 'required|exists:nc_call_types,id',
            'callCategoryId' => 'required|exists:nc_call_categories,id',
            'callSubCategoryId' => 'required|exists:nc_call_sub_categories,id',
            'callerMobileNo' => 'required|string',
            'requiredField' => 'nullable|array',
            'comments' => 'nullable|string',
            'attachment' => 'nullable|string',
            // |mimes:jpeg,png,jpg,gif,pdf,doc,docx,xls,xlsx',
        ]);

        $ticket = $this->ticketService->createTicket($validated);

        return response()->json($ticket, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NCTicket  $nCTicket
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $ticket = NCTicket::with(['callType', 'callCategory', 'callSubCategory'])
            ->findOrFail($id);
        $statuses = $this->ticketService->getStatuses();

        $ticketCollection = collect($ticket);
        $ticketCollection->put('statuses', $statuses);
        $requiredFields = TicketsRequiredField::where('ticket_id', $id)
            ->with('requiredFields')
            ->get()
            ->map(function ($field) {
                return [
                    'required_field_id' => $field->required_field_id,
                    'required_field_name' => $field->requiredFields->input_field_name,
                    'required_field_value' => $field->required_field_value,
                ];
            });

        $ticketCollection->put('required_fields', $requiredFields);
        return response()->json($ticketCollection, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\NCTicket  $nCTicket
     * @return \Illuminate\Http\Response
     */
    public function edit(NCTicket $nCTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NCTicket  $nCTicket
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $ticket = $this->ticketService->updateTicket($request, $id);
        return response()->json($ticket, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NCTicket  $nCTicket
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        $Ticket = NCTicket::findOrFail($id);

        // Delete associated NCTicketTimeline records
        $Ticket->NCTicketTimelines()->delete();

        // Delete the NCTicket
        $Ticket->delete();

        return response()->json([
            'title' => 'Success.',
            'message' => 'Ticket deleted.',
            'data' => '',
        ], 200);
    }

    public function getPreviousTicket(Request $request, $mobileNo)
    {
        $tickets = NCTicket::with(['callSubCategory'])
            ->where('caller_mobile_no', $mobileNo)
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($ticket) {
                return [
                    'ticket_id' => $ticket->id,
                    'ticket_created_at' => $ticket->ticket_created_at,
                    'uuid' => $ticket->uuid,
                    'ticket_status' => $ticket->ticket_status,
                    'call_sub_category_name' => $ticket->callSubCategory->call_sub_category_name,
                ];
            });

        return response()->json($tickets);
    }

    public function assignTicket(Request $request, $id)
    {
        $ticket = NCTicket::find($id);
        $authUserId = Auth::id();
        $now = Carbon::now();
        $authUserGroupId = Auth::user()->group_id;

        // dd();

        // if group owner is visiting the ticket
        /* if (Auth::user()->group->hasOwner() || Auth::user()->hasRole('admin') || Auth::user()->hasRole('superadmin')) {
        return response()->json([
        'success' => true,
        'showAlert' => false,
        'message' => 'Ticket page visited by admin/owner user.',
        ]);

        } */

        if ($ticket->initial_assign_id === null || $ticket->assign_to_user_id === null) {
            DB::transaction(function () use ($ticket, $authUserId, $authUserGroupId) {
                $ticket->initial_assign_id = $ticket->assign_to_user_id = $authUserId;
                $ticket->assign_to_group_id = $authUserGroupId;
                $ticket->save();
            });

            // after ticket assignment add a ticket timeline
            $data = [
                'ticket_id' => $ticket->id,
                'responsible_group_ids' => $ticket->responsible_group_ids,
                'ticket_status' => 'OPENED',
                'ticket_comments' => $ticket->comments,
                'ticket_attachments' => $ticket->ticket_attachments,
                'ticket_opened_by' => $authUserId,
                'ticket_status_updated_by' => $authUserId,
                'opened_at' => $now,
                'last_time_opened_at' => $now,
            ];

            $this->ticketService->createTicketTimeline($data);

            return response()->json([
                'success' => true,
                'showAlert' => true,
                'message' => 'Ticket assigned to you successfully.',
            ]);
        }

        // If the ticket is already assigned to another user
        return response()->json([
            'success' => false,
            'showAlert' => true,
            'message' => 'This ticket is already engaged by another user.',
            'assign_to_user_id' => $ticket->assign_to_user_id,
            'assigned_user_name' => User::find($ticket->assign_to_user_id)->name,
        ], 400);
    }

    public function ticketStatus($id)
    {
        $ticket = NCTicket::find($id);

        if ($ticket->initial_assign_id === null || $ticket->assign_to_user_id === null) {
            return response()->json([
                'engaged' => false,
                'message' => 'This ticket is not yet assigned.',
            ]);
        }

        return response()->json([
            'engaged' => true,
            'assign_to_user_id' => $ticket->assign_to_user_id,
            'assigned_user_name' => User::find($ticket->assign_to_user_id)->name ?? "Unknown",
            'message' => 'This ticket is already engaged by another user.',
        ]);
    }

    public function forwardTicket(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'forward_type' => 'required|in:user,group',
            'forward_to' => 'required',
            'comments' => 'required|string',
        ]);

        $ticketId = $id;
        $comment = $validated['comments'];
        $now = Carbon::now();
        $ticketStatus = 'REOPEN';

        try {
            // Fetch the ticket by ID
            $ticket = NCTicket::findOrFail($ticketId);
            $ticket->is_ticket_reassign = 1;
            $ticket->ticket_updated_at = $now;

            if ($validated['forward_type'] === 'user') {
                $this->forwardToUser($ticket, $validated['forward_to'], $comment, $ticketStatus, $now);
            } elseif ($validated['forward_type'] === 'group') {
                $this->forwardToGroup($ticket, $validated['forward_to'], $comment, $ticketStatus, $now);
            }

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Ticket has been successfully forwarded.',
            ]);

        } catch (\Exception $e) {
            Log::error('TICKET-FORWARDING-ERROR|' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to forward the ticket.',
            ], 500);
        }
    }

    public function addTimelineForFirstTimePageLoad(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'user_id' => 'required',
            'comments' => 'nullable|string',
        ]);

        $ticketId = $id;
        $userId = $validated['user_id'];
        $comment = 'Ticket page just opened by a user.';
        $now = Carbon::now();

        try {
            // Fetch the ticket by ID
            $ticket = NCTicket::findOrFail($ticketId);

            $data = [
                'ticket_id' => $ticket->id,
                'responsible_group_ids' => $ticket->responsible_group_ids,
                'ticket_status' => 'OPENED',
                'ticket_comments' => $comment,
                'ticket_attachments' => $ticket->attachment,
                'ticket_opened_by' => $userId,
                'ticket_status_updated_by' => $userId,
                'opened_at' => $now,
                'last_time_opened_at' => $now,
            ];

            $this->ticketService->createTicketTimeline($data);

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Ticket timeline data added.',
            ]);

        } catch (\Exception $e) {
            Log::error('TICKET-TIMELINE-ERROR|' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to save ticket timeline data.',
            ], 500);
        }
    }

/**
 * Handle forwarding the ticket to a specific user.
 */
    private function forwardToUser($ticket, $userId, $comment, $ticketStatus, $now)
    {
        $user = User::where('id', $userId)
            ->where('status', 'Active')
            ->first();

        if ($user) {
            $ticket->assign_to_user_id = $user->id;
            $ticket->assign_to_group_id = $user->group_id;

            $this->createTicketTimelineAndNotify($ticket, $user->id, $comment, $ticketStatus, $now);
            $ticket->save();
        } else {
            // Handle invalid user case
        }
    }

/**
 * Handle forwarding the ticket to a specific group.
 */
    private function forwardToGroup($ticket, $groupId, $comment, $ticketStatus, $now)
    {
        $group = Group::find($groupId);

        if ($group) {
            $users = User::where('group_id', $group->id)
                ->where('status', 'Active')
                ->get();

            foreach ($users as $user) {
                $ticket->assign_to_user_id = $user->id;
                $ticket->assign_to_group_id = $group->id;

                $this->createTicketTimelineAndNotify($ticket, $user->id, $comment, $ticketStatus, $now);
            }
            $ticket->save();
        } else {
            // Handle invalid group case
        }
    }

/**
 * Create a ticket timeline and send a notification to the user.
 */
    private function createTicketTimelineAndNotify($ticket, $userId, $comment, $ticketStatus, $now)
    {
        $data = [
            'ticket_id' => $ticket->id,
            'responsible_group_ids' => $ticket->responsible_group_ids,
            'ticket_status' => $ticketStatus,
            'ticket_comments' => $comment,
            'ticket_attachments' => $ticket->attachment,
            'ticket_opened_by' => $userId,
            'ticket_status_updated_by' => $userId,
            'opened_at' => $now,
            'last_time_opened_at' => $now,
        ];

        $this->ticketService->createTicketTimeline($data);

        $fetchServiceTypeConfigs = $this->serviceTypeConfig->getServiceTypeConfigs(
            $ticket->call_type_id,
            $ticket->call_category_id,
            $ticket->call_sub_category_id
        );

        $this->notificationService->sendTicketNotificationToUser($ticket, $fetchServiceTypeConfigs, $userId);
    }

}
