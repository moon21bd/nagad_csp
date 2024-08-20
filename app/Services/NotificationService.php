<?php

namespace App\Services;

use App\Http\Controllers\NCNotificationController;
use App\Jobs\SendTicketNotificationJob;
use App\Models\EmailConfig;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Swift_Mailer;
use Swift_SmtpTransport;

class NotificationService
{
    public function sendTicketNotification($ticket, $serviceTypeConfigs, $responsibleGroupIdsStr)
    {
        try {
            // Retrieve email configuration
            $getEmailConfig = EmailConfig::findOrFail($serviceTypeConfigs->email_config_id);

            // Create a custom SwiftMailer instance
            $swiftMailer = $this->createCustomMailer($getEmailConfig);

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

                // Dispatch the job to the queue
                SendTicketNotificationJob::dispatch($ticket, $notificationData, $user->email, $getEmailConfig->toArray());

                /* try {
            // Use the custom SwiftMailer instance to send emails
            $message = (new \Swift_Message('New Ticket Created'))
            ->setFrom([$getEmailConfig['from_address'] => $getEmailConfig['from_name']])
            ->setTo($user->email)
            ->setBody(view('emails.ticket_notification', ['ticket' => $ticket, 'notificationData' => $notificationData])->render(), 'text/html');

            $swiftMailer->send($message);
            } catch (\Exception $e) {
            Log::error('MAIL-ERROR|' . $e->getMessage() . '|USER-ID|' . $user->email);
            } */
            }
        } catch (\Exception $e) {
            Log::error('NOTIFICATION-ERROR|' . $e->getMessage());
        }
    }

    /**
     * Create a custom SwiftMailer instance with provided email configuration.
     *
     * @param \App\Models\EmailConfig $emailConfig
     * @return \Swift_Mailer
     */
    protected function createCustomMailer($emailConfig)
    {
        $transport = (new Swift_SmtpTransport($emailConfig['host'], $emailConfig['port'], $emailConfig['encryption']))
            ->setUsername($emailConfig['username'])
            ->setPassword($emailConfig['password']);

        return new Swift_Mailer($transport);
    }
}
