<?php
namespace App\Listeners;

use App\Events\TicketCreated;
use App\Mail\TicketNotificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTicketNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\TicketCreated  $event
     * @return void
     */
    public function handle(TicketCreated $event)
    {
        $ticket = $event->ticket;

        // Retrieve email configuration
        $emailConfig = $this->getEmailConfig();

        // Send email using Laravel's Mail facade
        Mail::to($ticket->recipient_email)->send(new TicketNotificationMail($ticket, $emailConfig));
    }

    /**
     * Get email configuration.
     *
     * @return array
     */
    protected function getEmailConfig()
    {
        // Your logic to retrieve email configuration
        return [
            'mailer' => 'smtp',
            'host' => 'smtp.office365.com',
            'port' => 587,
            'username' => 'noreply@sohoj.my',
            'password' => 'D9K!4E&hS2^j#gM$',
            'encryption' => 'tls',
            'from_address' => 'noreply@sohoj.my',
            'from_name' => 'Nagad',
        ];
    }
}
