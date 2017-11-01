<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\EmailLog;
use Tests\TestCase;
use App\DemonstratorApplication;
use Illuminate\Support\Facades\Event;
use App\Notifications\StudentContractReady;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AcademicAcceptsStudent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Events\NotificationSent;

class EmailLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function sending_a_notification_is_logged ()
    {
        config(['mail.driver' => 'log']);

        $application = factory(DemonstratorApplication::class)->create(['is_accepted' => true]);

        $application->student->notify(new AcademicAcceptsStudent($application));
        $application->student->notify(new StudentContractReady('Fred'));

        $this->assertDatabaseHas('email_logs', ['user_id' => $application->student->id, 'notification' => AcademicAcceptsStudent::class]);
        $this->assertDatabaseHas('email_logs', ['user_id' => $application->student->id, 'notification' => StudentContractReady::class]);
    }

    /** @test */
    public function a_notification_log_has_a_user ()
    {
        $log = factory(EmailLog::class)->create();

        $this->assertNotNull($log->user);
    }
}
