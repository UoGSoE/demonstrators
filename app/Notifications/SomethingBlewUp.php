<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;

class SomethingBlewUp extends Notification
{
    use Queueable;

    public $exception;

    public function __construct($exception)
    {
        $this->exception = $exception;
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content("Exception! Argh! I'm Dying!")
            ->attachment(function ($attachment) {
                $attachment->title($this->exception->getMessage())
                    ->fields([
                        'line' => $this->exception->getLine(),
                        'file' => $this->exception->getFile(),
                        'summary' => $this->getExceptionTrace(),
                    ]);
            });
    }

    protected function getExceptionTrace()
    {
        $text = '';
        $entries = [];
        foreach ($this->exception->getTrace() as $entry) {
            if (array_key_exists('class', $entry)) {
                if (preg_match('/App/', $entry['class'])) {
                    $line = 'Unknown';
                    $function = 'Unknown';
                    if (array_key_exists('line', $entry)) {
                        $line = $entry['line'];
                    }
                    if (array_key_exists('function', $entry)) {
                        $function = $entry['function'];
                    }

                    $entries[] = $entry['class'] . ' / line "' . $line . '" / function "' . $function . '"';
                }
            }
        }
        $guff = substr($this->exception->__toString(), 0, 500);
        return implode("\n", $entries) . "\n" . $guff;
    }
}
