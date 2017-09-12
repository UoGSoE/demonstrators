<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AcademicAcceptsStudent extends Notification
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
            ->subject($this->getSubject())
            ->markdown('emails.student.accepted', [
                'application' => $this->application
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

    public function shouldBeSkipped()
    {
        //always ensures we have a fresh application whether this is queued or not
        if (!$this->application->fresh()->is_accepted) {
            return true;
        }
        return false;
    }

    protected function getSubject()
    {
        return $this->application->request->course->code.' '.$this->application->request->course->title.' - '.$this->application->request->type.' Accepted';
    }
}
