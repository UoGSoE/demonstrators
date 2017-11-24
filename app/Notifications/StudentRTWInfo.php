<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StudentRTWInfo extends Notification
{
    use Queueable;

    public $forenames;
    public $subject;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($forenames)
    {
        $this->forenames = $forenames;
        $this->subject = $this->getSubject();
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
            ->subject($this->subject)
            ->markdown('emails.student.rtw', ['forenames' => $this->forenames])
            ->attach(asset('files/EWP-registration-form-July-2016.doc'));
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
        return 'School of Engineering - Acceptance Confirmation - RtW Required';
    }
}
