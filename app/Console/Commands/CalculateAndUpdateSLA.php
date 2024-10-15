<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\NCServiceResponsibleGroup;
use App\Models\NCServiceTypeConfig;
use App\Models\NCTicket;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateAndUpdateSLA extends Command
{
    protected $signature = 'sla:update';
    protected $description = 'Check SLA and forward tickets if necessary';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {

        $tickets = NcTicket::whereNotIn('ticket_status', ['CLOSED', 'CLOSED - REACHED', 'CLOSED - NOT RECEIVED', 'CLOSED - NOT CONNECTED', 'CLOSED - SWITCHED OFF', 'CLOSED - NOT COOPERATED'])
            ->whereNotNull('responsible_group_ids')
            ->get();

        foreach ($tickets as $ticket) {
            // Check if the ticket is already closed
            if (in_array($ticket->ticket_status, ['CLOSED', 'CLOSED - REACHED', 'CLOSED - NOT RECEIVED', 'CLOSED - NOT CONNECTED', 'CLOSED - SWITCHED OFF', 'CLOSED - NOT COOPERATED'])) {
                $ticket->sla_status = 'met'; // Ticket resolved within SLA time
                $ticket->sla_updated_at = Carbon::now(); // Update SLA timestamp to closure time
                $ticket->save();
                continue; // Move to the next ticket
            }

            $currentGroupId = $ticket->assign_to_group_id;

            while ($currentGroupId) {
                $group = NCServiceResponsibleGroup::where('group_id', $currentGroupId)->first();

                if ($group) {
                    // Calculate SLA deadline based on sla_updated_at
                    $slaDeadline = Carbon::parse($ticket->sla_updated_at ?? $ticket->ticket_created_at)
                        ->addHours($group->tat_hours);

                    if (Carbon::now()->greaterThan($slaDeadline)) {
                        // SLA breached; escalate if possible
                        $ticket->sla_status = 'breached';

                        if ($group->next_group_id) {
                            $ticket->assign_to_group_id = $group->next_group_id;
                            $ticket->sla_updated_at = Carbon::now(); // Update SLA-specific timestamp
                            $ticket->save();

                            // Move to next group in escalation chain
                            $currentGroupId = $group->next_group_id;
                            continue;
                        } else {
                            $ticket->sla_status = 'review_needed';
                            break;
                        }
                    } else {
                        // SLA is within deadline; no further escalation needed
                        $ticket->sla_status = 'in_progress';
                        break;
                    }
                } else {
                    // If no group found, exit escalation loop
                    break;
                }
            }

            // Final update to ticket, using sla_updated_at for SLA-specific updates
            $ticket->ticket_updated_at = Carbon::now(); // Optionally for other tracking
            $ticket->save();
        }

    }

    private function notifyGroupOwner($ticket)
    {
        $groupId = $ticket->assign_to_group_id;
        $group = Group::find($groupId);

        if ($group) {
            $owner = User::where('group_id', $groupId)->where('is_owner', true)->first();

            if ($owner) {
                // Send notification to the group owner
                $this->notificationService->sendNotificationToUser($owner, 'SLA failed for ticket ' . $ticket->id);
            }
        }
    }

    private function notifyNextGroup($ticket)
    {
        $slaConfig = NCServiceTypeConfig::where('call_type_id', $ticket->call_type_id)
            ->where('call_category_id', $ticket->call_category_id)
            ->where('call_sub_category_id', $ticket->call_sub_category_id)
            ->first();

        if ($slaConfig && $slaConfig->next_group_id) {
            $nextGroup = Group::find($slaConfig->next_group_id);

            if ($nextGroup) {
                $users = User::where('group_id', $nextGroup->id)->get();

                foreach ($users as $user) {
                    // Send notification to the users in the next group
                    $this->notificationService->sendNotificationToUser($user, 'Ticket forwarded to your group due to SLA failure');
                }

                // Update ticket assignment
                $ticket->assign_to_group_id = $nextGroup->id;
                $ticket->sla_updated_at = Carbon::now();
                $ticket->save();
            }
        }
    }
}
