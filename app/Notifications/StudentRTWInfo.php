<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class StudentRTWInfo extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($application)
    {
        $this->application = $application;
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
        if ($this->shouldBeSkipped()) {
            return;
        }
        return (new MailMessage)
            ->subject($this->subject)
            ->markdown('emails.student.rtw');
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

    public function shouldBeSkipped()
    {
        if ($this->application->student->fresh()->rtw_notified) {
            return true;
        }
        return false;
    }

    protected function getSubject()
    {
        return 'RTW notif';
    }
}
