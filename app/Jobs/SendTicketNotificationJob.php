<?php
namespace App\Jobs;

use App\Mail\TicketNotificationMail; // Ensure this Mailable class exists
use App\Models\EmailConfig;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTicketNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ticket;
    protected $notificationData;
    protected $userEmail;
    protected $emailConfig;

    /**
     * Create a new job instance.
     *
     * @param $ticket
     * @param $notificationData
     * @param $userEmail
     * @param $emailConfig
     */
    public function __construct($ticket, $notificationData, $userEmail, $emailConfig)
    {
        $this->ticket = $ticket;
        $this->notificationData = $notificationData;
        $this->userEmail = $userEmail;
        $this->emailConfig = $emailConfig;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = (new \Swift_Message('New Ticket Created'))
            ->setFrom([$this->emailConfig['from_address'] => $this->emailConfig['from_name']])
            ->setTo($this->userEmail)
            ->setBody(view('emails.ticket_notification', ['ticket' => $this->ticket, 'notificationData' => $this->notificationData])->render(), 'text/html');

        $transport = (new \Swift_SmtpTransport($this->emailConfig['host'], $this->emailConfig['port'], $this->emailConfig['encryption']))
            ->setUsername($this->emailConfig['username'])
            ->setPassword($this->emailConfig['password']);

        $swiftMailer = new \Swift_Mailer($transport);

        $swiftMailer->send($message);
    }
}
