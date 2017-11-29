<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StudentConfirmsRTWCompleted extends Notification
{
    use Queueable;

    public $application;
    public $forenames;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($application, $forenames)
    {
        $this->application = $application;
        $this->forenames = $forenames;
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
            ->markdown('emails.student.confirmed_rtw_completed', ['forenames' => $this->forenames])
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
        return 'School of Engineering - Acceptance Confirmation';
    }
}
