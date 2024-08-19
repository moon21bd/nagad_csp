<?php

namespace App\Mail;

use App\Models\NCTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $notificationData;
    public $emailConfig;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\NCTicket  $ticket
     * @param  array  $notificationData
     * @param  array  $emailConfig
     * @return void
     */
    public function __construct(NCTicket $ticket, array $notificationData, array $emailConfig)
    {
        $this->ticket = $ticket;
        $this->notificationData = $notificationData;
        $this->emailConfig = $emailConfig;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ticket_notification')
            ->with([
                'ticket' => $this->ticket,
                'notificationData' => $this->notificationData,
            ])
            ->from($this->emailConfig['from_address'], $this->emailConfig['from_name'])
            ->subject('New Ticket Created');
    }
}
