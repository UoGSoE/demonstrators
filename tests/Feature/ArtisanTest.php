<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\DemonstratorApplication;
use App\DemonstratorRequest;
use App\User;
use App\Notifications\AcademicStudentsApplied;
use App\Notifications\AcademicStudentsConfirmation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ArtisanTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function we_can_send_an_academic_a_bundled_email_of_new_applications () {
        Notification::fake();
        $staff = factory(User::class)->states('staff')->create();
        $request1 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id]);
        $newApplications1 = factory(DemonstratorApplication::class, 3)->create(['request_id' => $request1->id]);
        $oldApplications1 = factory(DemonstratorApplication::class, 2)->create(['request_id' => $request1->id, 'is_new' => false]);
        $newApplications2 = factory(DemonstratorApplication::class, 5)->create(['request_id' => $request2->id]);
        $oldApplications2 = factory(DemonstratorApplication::class, 4)->create(['request_id' => $request2->id, 'is_new' => false]);

        $staff->sendNewApplicantsEmail();
        Notification::assertSentTo($staff, AcademicStudentsApplied::class, function ($notification, $channels) {
                return $notification->applications->count() == 8;
        });
    }

    /** @test */
    public function academic_is_not_emailed_if_no_new_applications_exist () {
        Notification::fake();
        $staff = factory(User::class)->states('staff')->create();
        $request1 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id]);
        $oldApplications1 = factory(DemonstratorApplication::class, 2)->create(['request_id' => $request1->id, 'is_new' => false]);

        $staff->sendNewApplicantsEmail();
        Notification::assertNotSentTo($staff, AcademicStudentsApplied::class);
    }

    /** @test */
    public function we_can_send_an_academic_a_bundled_email_of_new_confirmations () {
        Notification::fake();
        $staff = factory(User::class)->states('staff')->create();
        $request1 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id]);

        $confirmedApplications = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id, 'student_confirms' => true, 'student_responded' => true]);
        $confirmedApplication2 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id, 'student_confirms' => true, 'student_responded' => true]);
        $declinedApplication1 = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id, 'student_confirms' => false, 'student_responded' => true]);

        $unconfirmedApplication1 = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id, 'student_responded' => false]);
        $unconfirmedApplication2 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id, 'student_responded' => false]);

        $applicationAlreadyNotified = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id, 'student_confirms' => true, 'student_responded' => true, 'academic_notified' => true]);

        $this->assertEquals($staff->newConfirmations()->count(), 3);
        $this->assertEquals(DemonstratorApplication::count(), 6);

        $staff->sendNewConfirmationsEmail();
        Notification::assertSentTo($staff, AcademicStudentsConfirmation::class, function ($notification, $channels) {
                return $notification->applications->count() == 3;
        });

        $this->assertEquals(DemonstratorApplication::count(), 5);
        $this->assertEquals($staff->newConfirmations()->count(), 0);
    }
}