<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AcademicStudentsApplied extends Notification
{
    use Queueable;

    public $applications;
    public $academic;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($applications, $academic)
    {
        $this->applications = $applications;
        $this->academic = $academic;
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
            ->markdown('emails.staff.new_applications', [
                'academic' => $this->academic,
                'applications' => $this->applications,
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
        return 'Demonstrators - New Applications';
    }
}
