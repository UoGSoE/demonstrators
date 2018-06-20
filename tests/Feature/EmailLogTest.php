<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\EmailLog;
use Tests\TestCase;
use Carbon\Carbon;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use Illuminate\Support\Facades\Event;
use App\Notifications\StudentContractReady;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AcademicAcceptsStudent;
use Illuminate\Notifications\Events\NotificationSent;

class EmailLogTest extends TestCase
{
    /** @test */
    public function sending_a_notification_is_logged ()
    {
        config(['mail.driver' => 'log']);

        $application = factory(DemonstratorApplication::class)->create(['is_accepted' => true]);

        $application->student->notify(new AcademicAcceptsStudent($application));
        $application->student->notify(new StudentContractReady('Fred'));

        $this->assertDatabaseHas('email_logs', ['user_id' => $application->student->id, 'notification' => AcademicAcceptsStudent::class, 'application_id' => $application->id]);
        $this->assertDatabaseHas('email_logs', ['user_id' => $application->student->id, 'notification' => StudentContractReady::class]);
    }

    /** @test */
    public function a_notification_log_has_a_user ()
    {
        $log = factory(EmailLog::class)->create();

        $this->assertNotNull($log->user);
    }

    /** @test */
    public function can_lookup_the_log_for_a_given_type_of_email_for_a_given_user_and_a_given_demonstrator_request ()
    {
        config(['mail.driver' => 'log']);
        $log = factory(EmailLog::class)->create(['created_at' => new Carbon('a week ago')]);
        $request = factory(DemonstratorRequest::class)->create(['type' => 'Demonstrator']);
        $application = factory(DemonstratorApplication::class)->create(['is_accepted' => true, 'request_id' => $request->id]);

        $application->student->notify(new AcademicAcceptsStudent($application));

        $this->assertNotEquals('', $application->student->getDateOf('AcademicAcceptsStudent', $application->request, 'Demonstrator'));
        $this->assertEquals(Carbon::now()->format('d/m/Y H:i'), $application->student->getDateOf('AcademicAcceptsStudent', $application->request, 'Demonstrator'));
    }

    /** @test */
    public function can_lookup_the_log_for_a_given_type_of_email_for_a_given_user ()
    {
        config(['mail.driver' => 'log']);
        $log = factory(EmailLog::class)->create(['created_at' => new Carbon('a week ago')]);
        $request = factory(DemonstratorRequest::class)->create(['type' => 'Demonstrator']);
        $application = factory(DemonstratorApplication::class)->create(['is_accepted' => true, 'request_id' => $request->id]);

        $application->student->notify(new StudentContractReady('Fred'));

        $this->assertNotEquals('', $application->student->getDateOf('StudentContractReady'));
        $this->assertEquals(Carbon::now()->format('d/m/Y H:i'), $application->student->getDateOf('StudentContractReady'));
    }
}
