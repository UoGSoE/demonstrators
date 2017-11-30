<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use Artisan;
use App\User;
use App\EmailLog;
use Carbon\Carbon;
use Tests\TestCase;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use App\Notifications\NeglectedRequests;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AcademicStudentsApplied;
use App\Notifications\AcademicApplicantCancelled;
use App\Notifications\AcademicStudentsConfirmation;
use App\Notifications\StudentApplicationsCancelled;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ArtisanTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function we_can_run_an_artisan_command_to_send_an_academic_a_bundled_email_of_new_applications ()
    {
        Notification::fake();
        $newApplication = factory(DemonstratorApplication::class)->create();

        Artisan::call('demonstrators:newapplications');

        Notification::assertSentTo($newApplication->request->staff, AcademicStudentsApplied::class);
    }

    /** @test */
    public function we_can_run_an_artisan_command_to_send_an_academic_a_bundled_email_of_new_confirmations ()
    {
        Notification::fake();
        $newApplication = factory(DemonstratorApplication::class)->create(['student_confirms' => true, 'student_responded' => true]);
        $declinedApplication = factory(DemonstratorApplication::class)->create(['student_confirms' => false, 'student_responded' => true]);
        $emailLog = factory(EmailLog::class)->create(['application_id' => $declinedApplication->id]);

        Artisan::call('demonstrators:newconfirmations');

        Notification::assertSentTo($newApplication->request->staff, AcademicStudentsConfirmation::class);
        $this->assertDatabaseMissing('email_logs', ['id' => $emailLog->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $declinedApplication->id]);
    }

    /** @test */
    public function we_can_send_an_academic_a_bundled_email_of_neglected_applications () {
        Notification::fake();
        $application = factory(\App\DemonstratorApplication::class)->create(['created_at' => new Carbon('4 days ago'), 'academic_seen' => false]);

        Artisan::call('demonstrators:neglectedrequests');
        
        Notification::assertSentTo($application->request->staff, NeglectedRequests::class);
    }

    /** @test */
    public function we_can_send_students_and_staff_emails_for_cancelled_applications()
    {
        Notification::fake();
        $oldApplication = factory(DemonstratorApplication::class)->create(['is_accepted' => true, 'updated_at' => new Carbon('4 days ago')]);

        Artisan::call('demonstrators:applicationcancelled');

        Notification::assertSentTo($oldApplication->student, StudentApplicationsCancelled::class);
        Notification::assertSentTo($oldApplication->request->staff, AcademicApplicantCancelled::class);
    }
}
