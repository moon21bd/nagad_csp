<?php

namespace App\Services;

use App\Http\Controllers\NCNotificationController;
use Illuminate\Http\Request;

class NotificationService
{
    public function sendTicketNotification($ticket, $serviceTypeConfigs, $responsibleGroupIdsStr)
    {
        $link = route('tickets.show', ['ticket' => $ticket->id]);

        $notificationData = [
            'is_group_lead_notified' => $serviceTypeConfigs->is_group_lead_notified ?? 'no',
            'group_ids' => explode(',', $responsibleGroupIdsStr), // IDs of groups to notify
            'title' => 'New Ticket Created',
            'channel' => 'web',
            'link' => $link,
            'message' => 'New Ticket has been created under your domain. Click to check.',
        ];

        app(NCNotificationController::class)->store(new Request($notificationData));
    }
}
