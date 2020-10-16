<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Models\EmailLog;
use App\Notifications\AcademicAcceptsStudent;
use App\Notifications\StudentContractReady;
use Carbon\Carbon;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailLogTest extends TestCase
{
    /** @test */
    public function sending_a_notification_is_logged()
    {
        config(['mail.driver' => 'log']);

        $application = DemonstratorApplication::factory()->create(['is_accepted' => true]);

        $application->student->notify(new AcademicAcceptsStudent($application));
        $application->student->notify(new StudentContractReady('Fred'));

        $this->assertDatabaseHas('email_logs', ['user_id' => $application->student->id, 'notification' => AcademicAcceptsStudent::class, 'application_id' => $application->id]);
        $this->assertDatabaseHas('email_logs', ['user_id' => $application->student->id, 'notification' => StudentContractReady::class]);
    }

    /** @test */
    public function a_notification_log_has_a_user()
    {
        $log = EmailLog::factory()->create();

        $this->assertNotNull($log->user);
    }

    /** @test */
    public function can_lookup_the_log_for_a_given_type_of_email_for_a_given_user_and_a_given_demonstrator_request()
    {
        config(['mail.driver' => 'log']);
        $log = EmailLog::factory()->create(['created_at' => new Carbon('a week ago')]);
        $request = DemonstratorRequest::factory()->create(['type' => 'Demonstrator']);
        $application = DemonstratorApplication::factory()->create(['is_accepted' => true, 'request_id' => $request->id]);

        $application->student->notify(new AcademicAcceptsStudent($application));

        $this->assertNotEquals('', $application->student->getDateOf('AcademicAcceptsStudent', $application->request, 'Demonstrator'));
        $this->assertEquals(Carbon::now()->format('d/m/Y H:i'), $application->student->getDateOf('AcademicAcceptsStudent', $application->request, 'Demonstrator'));
    }

    /** @test */
    public function can_lookup_the_log_for_a_given_type_of_email_for_a_given_user()
    {
        config(['mail.driver' => 'log']);
        $log = EmailLog::factory()->create(['created_at' => new Carbon('a week ago')]);
        $request = DemonstratorRequest::factory()->create(['type' => 'Demonstrator']);
        $application = DemonstratorApplication::factory()->create(['is_accepted' => true, 'request_id' => $request->id]);

        $application->student->notify(new StudentContractReady('Fred'));

        $this->assertNotEquals('', $application->student->getDateOf('StudentContractReady'));
        $this->assertEquals(Carbon::now()->format('d/m/Y H:i'), $application->student->getDateOf('StudentContractReady'));
    }
}
