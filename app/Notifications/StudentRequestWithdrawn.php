<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentRequestWithdrawn extends Notification
{
    use Queueable;

    public $forenames;
    public $demonstratorRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($forenames, $demonstratorRequest)
    {
        $this->forenames = $forenames;
        $this->demonstratorRequest = $demonstratorRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->getSubject())
            ->markdown('emails.student.request_withdrawn', [
                'forenames' => $this->forenames,
                'demonstratorRequest' => $this->demonstratorRequest,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    protected function getSubject()
    {
        return 'Request withdrawn';
    }
}
