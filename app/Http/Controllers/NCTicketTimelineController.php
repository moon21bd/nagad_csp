<?php

namespace App\Http\Controllers;

use App\Models\NCTicket;
use App\Models\NCTicketTimeline;
use App\Models\TicketsRequiredField;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NCTicketTimelineController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\NCTicketTimeline  $nCTicketTimeline
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $ticket = NCTicket::with([
            'callType',
            'callCategory',
            'callSubCategory',
        ])->find($id);

        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found',
            ], 404);
        }

        $requiredFields = TicketsRequiredField::where('ticket_id', $id)
            ->with('requiredFields')
            ->get()
            ->map(function ($field) {
                return [
                    'required_field_name' => $field->requiredFields->input_field_name,
                    'required_field_value' => $field->required_field_value,
                ];
            });

        $timelines = NCTicketTimeline::where('ticket_id', $id)->get();

        $user = User::with('user_login_activity', 'user_details')->find($ticket->ticket_created_by);

        $userLoginActivities = $user ? $user->user_login_activity()
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get() : [];

        $comments = [];

        if ($ticket->comments) {
            $comments[] = [
                'date_time' => Carbon::parse($ticket->updated_at)->format('M j, Y h:i A'),
                'avatar_url' => $user->avatar_url ?? null,
                'comment' => $ticket->comments,
                'username' => $user->name ?? 'Unknown',
            ];
        }

        foreach ($timelines as $timeline) {
            if ($timeline->ticket_comments) {
                $commentingUser = User::find($timeline->ticket_opened_by) ?: User::find($timeline->ticket_status_updated_by);

                $comments[] = [
                    'date_time' => Carbon::parse($timeline->updated_at)->format('M j, Y h:i A'),
                    'avatar_url' => $commentingUser->avatar_url ?? null,
                    'comment' => $timeline->ticket_comments,
                    'username' => $commentingUser->name ?? 'Unknown',
                ];
            }
        }

        $response = [
            'ticket_id' => $ticket->id,
            'ticket' => [
                'id' => $ticket->id,
                'caller_mobile_no' => $ticket->caller_mobile_no,
                'assign_to_user_id' => $ticket->assign_to_user_id,
                'comments' => $comments,
                'sla_status' => $ticket->sla_status,
                'ticket_status' => $ticket->ticket_status,
                'ticket_channel' => $ticket->ticket_channel,
                'ticket_created_by' => $ticket->ticket_created_by,
                'call_type' => $ticket->callType ? $ticket->callType->call_type_name : null,
                'call_category' => $ticket->callCategory ? $ticket->callCategory->call_category_name : null,
                'call_sub_category' => $ticket->callSubCategory ? $ticket->callSubCategory->call_sub_category_name : null,
            ],
            'group_names' => $ticket->responsible_group_names,
            'required_fields' => $requiredFields,
            'agent_user_info' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'employee_id' => $user->user_details->employee_id,
                'employee_name' => $user->user_details->employee_name,
                'employee_user_id' => $user->employee_user_id ?? "",
                'group_id' => $user->group_id,
                'group_name' => $user->group->name ?? null,
                'mobile_no' => $user->mobile_no,
                'email' => $user->email,
                'ip_address' => $userLoginActivities->first()->ip_address ?? null,
                'last_login' => $userLoginActivities->first()->last_login,
                'device_name' => $userLoginActivities->first()->browser . ", " . $userLoginActivities->first()->login_device_name ?? null,
            ] : null,
            'timelines' => $timelines->map(function ($item) {
                $user = User::find($item->ticket_status_updated_by);

                return [
                    'id' => $item->id,
                    'ticket_status' => $item->ticket_status,
                    'ticket_comments' => $item->ticket_comments,
                    'ticket_attachments' => $item->ticket_attachments,
                    'ticket_opened_by' => $item->ticket_opened_by,
                    'ticket_status_updated_by' => $item->ticket_status_updated_by,
                    'username' => $user->name ?? 'Unknown',
                    'user_id' => $user->user_details->employee_id ?? $user->id,
                    'opened_at' => $item->opened_at,
                    'last_time_opened_at' => $item->last_time_opened_at,
                    'created_at' => Carbon::parse($item->created_at)->format('M j, Y h:i A'),
                    'updated_at' => Carbon::parse($item->updated_at)->format('M j, Y h:i A'),
                ];
            }),

        ];

        return response()->json([
            'data' => $response,
        ]);
    }
    public function edit(NCTicketTimeline $nCTicketTimeline)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\NCTicketTimeline  $nCTicketTimeline
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NCTicketTimeline $nCTicketTimeline)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\NCTicketTimeline  $nCTicketTimeline
     * @return \Illuminate\Http\Response
     */
    public function destroy(NCTicketTimeline $nCTicketTimeline)
    {
        //
    }
}
