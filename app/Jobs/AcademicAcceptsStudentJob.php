<?php

namespace App\Jobs;

use App\Notifications\AcademicAcceptsStudent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AcademicAcceptsStudentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $application;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($application)
    {
        $this->application = $application;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->shouldBeSkipped()) {
            return;
        }
        $this->application->student->notify(new AcademicAcceptsStudent($this->application));
    }

    public function shouldBeSkipped()
    {
        //always ensures we have a fresh application whether this is queued or not
        if (! $this->application->fresh()->is_accepted) {
            return true;
        }

        return false;
    }
}
