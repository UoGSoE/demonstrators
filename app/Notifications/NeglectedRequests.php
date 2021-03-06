<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NeglectedRequests extends Notification
{
    use Queueable;

    public $requests;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($requests)
    {
        $this->requests = $requests;
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
        return (new MailMessage)->subject($this->getSubject())->markdown('emails.staff.neglectedrequests', [
            'requests' => $this->requests
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

    public function getSubject()
    {
        return 'Outstanding Demonstrator Applications';
    }
}
