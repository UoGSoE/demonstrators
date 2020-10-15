<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Course;
use App\DegreeLevel;
use App\DemonstratorApplication;
use App\DemonstratorRequest;
use App\EmailLog;
use App\Notifications\AcademicApplicantCancelled;
use App\Notifications\StudentApplicationsCancelled;
use App\Notifications\StudentConfirmsRTWCompleted;
use App\Notifications\StudentConfirmsRTWNotified;
use App\Notifications\StudentConfirmWithContract;
use App\Notifications\StudentRTWInfo;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StudentTest extends TestCase
{
    /** @test */
    public function student_can_see_list_of_requests()
    {
        $student = factory(User::class)->states('student')->create();
        $request1 = factory(DemonstratorRequest::class)->create(['hours_training' => 555, 'demonstrators_needed' => 777]);
        $request2 = factory(DemonstratorRequest::class)->create();
        $degreeLevel = factory(DegreeLevel::class)->create();
        $request1->staff->courses()->attach($request1->course);
        $request2->staff->courses()->attach($request2->course);
        $request1->degreeLevels()->attach($degreeLevel);

        $response = $this->actingAs($student)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($request1->course->title);
        $response->assertSee((string) $request1->hours_needed);
        $response->assertSee((string) $request1->hours_training);
        $response->assertSee((string) $request1->demonstrators_needed);
        $response->assertSee($degreeLevel->title);
        $response->assertSee($request2->course->title);
        $response->assertSee((string) $request2->hours_needed);
    }

    /** @test */
    public function student_can_apply_for_a_request()
    {
        $student = factory(User::class)->states('student')->create();
        $request = factory(DemonstratorRequest::class)->create();

        $response = $this->actingAs($student)->post(route('application.apply', $request->id), ['hours' => 2]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $application = DemonstratorApplication::first();
        $this->assertEquals($student->id, $application->student_id);
        $this->assertEquals($request->id, $application->request_id);
    }

    /** @test */
    public function student_can_change_their_notes()
    {
        $student = factory(User::class)->states('student')->create();

        $response = $this->actingAs($student)->post(route('student.profile.update', $student), ['notes' => 'This is my notes.']);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertEquals($student->fresh()->notes, 'This is my notes.');
    }

    /** @test */
    public function student_can_set_their_degree_level()
    {
        $student = factory(User::class)->states('student')->create(['degree_level_id' => null]);
        $degreeLevel = factory(DegreeLevel::class)->create();

        $response = $this->actingAs($student)->post(route('student.profile.update', $student), ['degree_level_id' => $degreeLevel->id]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertEquals($student->fresh()->degreeLevel->id, $degreeLevel->id);
    }

    /** @test */
    public function student_can_unapply_for_a_request()
    {
        $application = factory(DemonstratorApplication::class)->create();
        $application2 = factory(DemonstratorApplication::class)->create();

        $response = $this->actingAs($application->student)->post(route('application.destroy', $application->request_id), ['withdraw' => true]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertEquals(1, DemonstratorApplication::count());
    }

    /** @test */
    public function student_cant_see_requests_that_have_accepted_max_students()
    {
        $student = factory(User::class)->states('student')->create();
        $request = factory(DemonstratorRequest::class)->create(['demonstrators_needed' => 1]);
        $request2 = factory(DemonstratorRequest::class)->create(['demonstrators_needed' => 1]);
        $request->staff->courses()->attach($request->course);
        $request2->staff->courses()->attach($request2->course);
        $application = factory(DemonstratorApplication::class)->create(['is_accepted' => 1, 'request_id' => $request->id]);

        $response = $this->actingAs($student)->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee($request->course->title);
        $response->assertSee($request2->course->title);
        $response->assertSee((string) $request2->hours_needed);
    }

    /** @test */
    public function students_can_see_their_acceptance_and_can_confirm_or_decline()
    {
        $application = factory(DemonstratorApplication::class)->create(['is_accepted' => true]);
        $application2 = factory(DemonstratorApplication::class)->create(['is_accepted' => false, 'student_responded' => false]);

        $response = $this->actingAs($application->student)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('student-positions');
        $response->assertSee($application->request->course->title);
        $response->assertDontSee("$application2->request->course->code $application2->request->course->title");
    }

    /** @test */
    public function students_can_confirm_their_acceptance()
    {
        Notification::fake();
        $application = factory(DemonstratorApplication::class)->create();

        $response = $this->actingAs($application->student)->post(route('application.studentconfirms', $application->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($application->fresh()->student_confirms);
        Notification::assertSentTo($application->student, StudentRTWInfo::class, function ($notification) use ($application) {
            $this->assertEquals($application->id, $notification->application->id);
            $markdown = app(\Illuminate\Mail\Markdown::class);
            $mail = $notification->toMail($application->student);
            $markdown->render($mail->markdown, $mail->data());

            return true;
        });
        //$this->assertDatabaseHas('email_logs', ['user_id' => $application->student->id, 'application_id' => $application->id, 'notification' => 'App\Notifications\StudentRTWInfo']);
    }

    /** @test */
    public function students_can_confirm_their_acceptance_after_already_receiving_rtw_info()
    {
        Notification::fake();
        $student = factory(User::class)->states('student')->create(['rtw_notified' => true]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);

        $response = $this->actingAs($application->student)->post(route('application.studentconfirms', $application->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($application->fresh()->student_confirms);
        Notification::assertSentTo($application->student, StudentConfirmsRTWNotified::class);
    }

    /** @test */
    public function students_can_confirm_their_acceptance_after_already_completing_rtw()
    {
        Notification::fake();
        $student = factory(User::class)->states('student')->create(['rtw_notified' => true, 'returned_rtw' => true]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);

        $response = $this->actingAs($application->student)->post(route('application.studentconfirms', $application->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($application->fresh()->student_confirms);
        Notification::assertSentTo($application->student, StudentConfirmsRTWCompleted::class);
    }

    /** @test */
    public function students_can_confirm_their_acceptance_that_already_has_a_contract()
    {
        Notification::fake();
        $student = factory(User::class)->states('student')->create(['has_contract' => true]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);

        $response = $this->actingAs($student)->post(route('application.studentconfirms', $application->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($application->fresh()->student_confirms);
        Notification::assertSentTo($application->student, StudentConfirmWithContract::class);
    }

    /** @test */
    public function students_can_decline_the_position()
    {
        $application = factory(DemonstratorApplication::class)->create();

        $response = $this->actingAs($application->student)->post(route('application.studentdeclines', $application->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
    }

    /** @test */
    public function students_can_confirm_their_acceptance_for_two_but_only_emailed_about_rtw_once()
    {
        Notification::fake();
        $application = factory(DemonstratorApplication::class)->create();
        $application2 = factory(DemonstratorApplication::class)->create();

        $response = $this->actingAs($application->student)->post(route('application.studentconfirms', $application->id));
        $response2 = $this->actingAs($application2->student)->post(route('application.studentconfirms', $application2->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($application->fresh()->student_confirms);
        $this->assertTrue($application2->fresh()->student_confirms);
        $this->assertTrue($application->student->fresh()->rtw_notified);
        //ensure only one is sent
        Notification::assertSentTo($application->student, StudentRTWInfo::class);
    }

    /** @test */
    public function students_applications_are_automatically_cancelled_if_they_do_not_respond_after_3_days()
    {
        Notification::fake();
        $student = factory(User::class)->states('student')->create();
        $oldApplications = factory(DemonstratorApplication::class, 2)->create(['student_id' => $student->id, 'is_accepted' => true, 'updated_at' => new Carbon('Last week')]);
        $newApplication = factory(DemonstratorApplication::class)->create(['student_id' => $student->id, 'is_accepted' => true, 'updated_at' => new Carbon('1 day ago')]);
        $emaillog = factory(EmailLog::class)->create(['application_id' => $oldApplications[0]->id]);

        $student->cancelIgnoredApplications();

        $this->assertCount(1, $student->applications);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $oldApplications[0]->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $oldApplications[1]->id]);
        $this->assertDatabaseMissing('email_logs', ['id' => $emaillog->id]);
        $this->assertDatabaseHas('demonstrator_applications', ['id' => $newApplication->id]);
    }

    /** @test */
    public function students_and_staff_are_notified_when_an_applications_is_automatically_cancelled()
    {
        Notification::fake();
        $student = factory(User::class)->states('student')->create();
        $oldApplications = factory(DemonstratorApplication::class, 2)->create(['student_id' => $student->id, 'is_accepted' => true, 'updated_at' => new Carbon('Last week')]);
        $newApplication = factory(DemonstratorApplication::class)->create(['student_id' => $student->id, 'is_accepted' => true, 'updated_at' => new Carbon('1 day ago')]);

        $student->cancelIgnoredApplications();

        Notification::assertSentTo($student, StudentApplicationsCancelled::class, function ($notification, $channels) use ($oldApplications, $newApplication) {
            $notification->applications->assertContains($oldApplications[0]);
            $notification->applications->assertContains($oldApplications[1]);
            $notification->applications->assertNotContains($newApplication);

            return true;
        });
        Notification::assertSentTo($oldApplications[0]->request->staff, AcademicApplicantCancelled::class, function ($notification, $channels) use ($oldApplications) {
            return $notification->application->id == $oldApplications[0]->id;
        });
        Notification::assertSentTo($oldApplications[1]->request->staff, AcademicApplicantCancelled::class, function ($notification, $channels) use ($oldApplications) {
            return $notification->application->id == $oldApplications[1]->id;
        });
        Notification::assertNotSentTo($newApplication->request->staff, AcademicApplicantCancelled::class);
    }

    /** @test */
    public function request_is_not_displayed_if_it_has_no_start_date()
    {
        $student = factory(User::class)->states('student')->create();
        $staff = factory(User::class)->states('staff')->create();
        $request1 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'start_date' => null, 'type' => 'Demonstrator']);
        $request2 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'start_date' => null, 'type' => 'Tutor', 'course_id' => $request1->course_id]);
        $staff->courses()->attach($request1->course);

        $response = $this->actingAs($student)->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee('Tutor');
    }
}
