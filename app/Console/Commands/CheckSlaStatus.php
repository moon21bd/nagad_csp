<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\NCServiceTypeConfig;
use App\Models\NCTicket;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSlaStatus extends Command
{
    protected $signature = 'sla:check';
    protected $description = 'Check SLA and forward tickets if necessary';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {

        Log::info('SLA Check Command Started');

        $tickets = NCTicket::whereNotNull('sla_updated_at')
            ->where('ticket_status', '<>', 'CLOSED')
            ->get();
        Log::info('Tickets Retrieved: ' . $tickets->count());
        dd('SLA Check Command Reached', $tickets->count());

        Log::info('Processing Ticket ID: ' . json_encode($tickets->toArray()));

        foreach ($tickets as $ticket) {
            $slaConfig = NCServiceTypeConfig::where('call_type_id', $ticket->call_type_id)
                ->where('call_category_id', $ticket->call_category_id)
                ->where('call_sub_category_id', $ticket->call_sub_category_id)
                ->first();

            if (!$slaConfig) {
                Log::error('SLA configuration not found for ticket ID: ' . $ticket->id);
                continue;
            }

            $slaHours = $slaConfig->sla_hours; // Assuming this is the correct field

            $createdAt = Carbon::parse($ticket->created_at);
            $lastUpdate = Carbon::parse($ticket->sla_updated_at);
            $currentTime = Carbon::now();

            $elapsedTime = $currentTime->diffInHours($lastUpdate);

            if ($elapsedTime > $slaHours) {
                // SLA Failed
                $ticket->sla_status = 'failed';
                $ticket->save();

                $this->notifyGroupOwner($ticket);
                $this->notifyNextGroup($ticket);

            } else {
                // Update SLA status as resolved if ticket is closed
                if ($ticket->ticket_status == 'closed') {
                    $ticket->sla_status = 'resolved';
                    $ticket->save();
                }
            }
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
