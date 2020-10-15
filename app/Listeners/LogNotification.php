<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;

class LogNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NotificationSent  $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        EmailLog::create([
            'user_id' => $event->notifiable->id,
            'notification' => get_class($event->notification),
            'application_id' => property_exists($event->notification, 'application') ?
                $event->notification->application->id : null,
        ]);
    }
}
