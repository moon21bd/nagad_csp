<?php

namespace App\Services;

use App\Http\Controllers\NCNotificationController;
use App\Mail\TicketNotificationMail;
use App\Models\EmailConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function sendTicketNotification($ticket, $serviceTypeConfigs, $responsibleGroupIdsStr)
    {
        // Retrieve email configuration
        $getEmailConfig = EmailConfig::findOrFail($serviceTypeConfigs->email_config_id);

        // Setup the mailer configuration
        config([
            'mail.default' => $getEmailConfig->mailer,
            'mail.mailers.smtp.host' => $getEmailConfig->host,
            'mail.mailers.smtp.port' => $getEmailConfig->port,
            'mail.mailers.smtp.username' => $getEmailConfig->username,
            'mail.mailers.smtp.password' => $getEmailConfig->password,
            'mail.mailers.smtp.encryption' => $getEmailConfig->encryption,
            'mail.from.address' => $getEmailConfig->from_address,
            'mail.from.name' => $getEmailConfig->from_name,
        ]);

        // Generate the notification data
        $link = route('tickets.show', ['ticket' => $ticket->id]);
        $notificationData = [
            'is_group_lead_notified' => $serviceTypeConfigs->is_group_lead_notified ?? 'no',
            'group_ids' => explode(',', $responsibleGroupIdsStr),
            'title' => 'New Ticket Created',
            'channel' => 'email',
            'link' => $link,
            'message' => 'New Ticket has been created under your domain. Click to check.',
        ];

        // Create notification request
        $notificationRequest = new Request($notificationData);

        // Store notifications using NCNotificationController
        app(NCNotificationController::class)->store($notificationRequest);

        // Fetch recipients for email notifications
        $recipients = User::whereIn('group_id', $notificationData['group_ids'])->get();

        // Dispatch email to queue for each recipient
        foreach ($recipients as $user) {
            // Skip if the user is a group lead and group lead notification is not allowed
            if ($notificationData['is_group_lead_notified'] === 'no' && $user->parent_id === 0) {
                continue;
            }

            Log::info('MAIL-SENT-FOR|' . json_encode($notificationData) . '|USER-ID|' . $user->email . '|CONFIG|' . json_encode($getEmailConfig->toArray()));

            Mail::to($user->email)->queue(new TicketNotificationMail($ticket, $notificationData, $getEmailConfig->toArray()));
        }
    }
}
