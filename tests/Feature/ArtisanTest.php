<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use Artisan;
use Carbon\Carbon;
use App\User;
use Tests\TestCase;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use App\Notifications\NeglectedRequests;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AcademicStudentsApplied;
use App\Notifications\AcademicStudentsConfirmation;
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

        Artisan::call('demonstrators:newconfirmations');

        Notification::assertSentTo($newApplication->request->staff, AcademicStudentsConfirmation::class);
    }

    /** @test */
    public function we_can_send_an_academic_a_bundled_email_of_neglected_applications () {
        Notification::fake();
        $application = factory(\App\DemonstratorApplication::class)->create(['created_at' => new Carbon('4 days ago')]);

        Artisan::call('demonstrators:neglectedrequests');
        
        Notification::assertSentTo($application->request->staff, NeglectedRequests::class);
    }
}
