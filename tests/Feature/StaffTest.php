<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\Course;
use App\Models\DegreeLevel;
use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Jobs\AcademicAcceptsStudentJob;
use App\Notifications\AcademicAcceptsStudent;
use App\Notifications\AcademicStudentsApplied;
use App\Notifications\AcademicStudentsConfirmation;
use App\Notifications\StudentRequestWithdrawn;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class StaffTest extends TestCase
{
    /** @test */
    public function staff_can_see_list_of_their_courses()
    {
        $staff = User::factory()->staff()->create();
        $courses = Course::factory()->count(3)->create();
        $staff->courses()->attach($courses);

        $response = $this->actingAs($staff)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($courses[0]->title);
        $response->assertSee($courses[1]->title);
        $response->assertSee($courses[2]->title);
    }

    /** @test */
    public function staff_can_submit_demonstrator_request_info()
    {
        $staff = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $staff->courses()->attach($course);

        $response = $this->actingAs($staff)->postJson(route('request.update', [
            'course_id' => $course->id,
            'start_date' => '10/11/2016',
            'hours_needed' => 10,
            'hours_training' => 1,
            'demonstrators_needed' => 2,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
            'staff_id' => $staff->id,
        ]));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertDatabaseHas('demonstrator_requests', [
            'course_id' => $course->id,
            'start_date' => Carbon::createFromFormat('d/m/Y', '10/11/2016')->format('Y-m-d'),
            'hours_needed' => 10,
            'hours_training' => 1,
            'demonstrators_needed' => 2,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
            'staff_id' => $staff->id,
        ]);
    }

    /** @test */
    public function invalid_data_is_rejected()
    {
        $staff = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $staff->courses()->attach($course);

        $response = $this->actingAs($staff)->postJson(route('request.update', [
            'course_id' => $course->id,
            'hours_needed' => 'Twelve',
            'demonstrators_needed' => 'Two',
            'hours_training' => '#Hours',
            'semester_1' => 'Yes',
            'semester_2' => 'Yes',
            'semester_3' => 'Yes',
            'skills' => 'Lasers',
            'staff_id' => $staff->id,
        ]));

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['hours_needed', 'hours_training', 'demonstrators_needed', 'type']]);
    }

    /** @test */
    public function staff_must_provide_at_least_one_semester()
    {
        $staff = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $staff->courses()->attach($course);

        $response = $this->actingAs($staff)->postJson(route('request.update', [
            'course_id' => $course->id,
            'start_date' => (new Carbon('next month'))->format('d/m/Y'),
            'hours_needed' => 10,
            'demonstrators_needed' => 2,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
            'staff_id' => $staff->id,
        ]));

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['semester_1', 'semester_2', 'semester_3']]);
    }

    /** @test */
    public function staff_can_see_demonstrator_applicants()
    {
        $staff = User::factory()->staff()->create();
        $courses = Course::factory()->count(3)->create();
        $request1 = DemonstratorRequest::factory()->create(['course_id' => $courses[0]->id, 'staff_id' => $staff->id]);
        $request2 = DemonstratorRequest::factory()->create(['course_id' => $courses[1]->id, 'staff_id' => $staff->id]);
        $request3 = DemonstratorRequest::factory()->create(['course_id' => $courses[2]->id, 'staff_id' => $staff->id]);
        $application1 = DemonstratorApplication::factory()->create(['request_id' => $request1->id]);
        $application2 = DemonstratorApplication::factory()->create(['request_id' => $request2->id]);
        $application3 = DemonstratorApplication::factory()->create(['request_id' => $request3->id]);
        $staff->courses()->attach($courses);

        $response = $this->actingAs($staff)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($application1->student->surname);
        $response->assertSee($application2->student->surname);
        $response->assertSee($application3->student->surname);
    }

    /** @test */
    public function staff_can_see_only_their_demonstrator_applicants()
    {
        $staff = User::factory()->staff()->create();
        $staff2 = User::factory()->staff()->create();
        $courses = Course::factory()->count(3)->create();
        $request1 = DemonstratorRequest::factory()->create(['course_id' => $courses[0]->id, 'staff_id' => $staff->id]);
        $request2 = DemonstratorRequest::factory()->create(['course_id' => $courses[1]->id, 'staff_id' => $staff->id]);
        $request3 = DemonstratorRequest::factory()->create(['course_id' => $courses[1]->id, 'staff_id' => $staff2->id]);
        $application1 = DemonstratorApplication::factory()->create(['request_id' => $request1->id]);
        $application2 = DemonstratorApplication::factory()->create(['request_id' => $request2->id]);
        $application3 = DemonstratorApplication::factory()->create(['request_id' => $request3->id]);
        $staff->courses()->attach($courses);
        $staff2->courses()->attach($courses);

        $response = $this->actingAs($staff)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($application1->student->surname);
        $response->assertSee($application2->student->surname);
        $response->assertDontSee($application3->student->surname);
    }

    /** @test */
    public function staff_can_toggle_accepted_status_on_applicants()
    {
        Notification::fake();
        $staff = User::factory()->staff()->create();
        $courses = Course::factory()->count(3)->create();
        $request1 = DemonstratorRequest::factory()->create(['course_id' => $courses[0]->id, 'staff_id' => $staff->id]);
        $request2 = DemonstratorRequest::factory()->create(['course_id' => $courses[1]->id, 'staff_id' => $staff->id]);
        $request3 = DemonstratorRequest::factory()->create(['course_id' => $courses[2]->id, 'staff_id' => $staff->id]);
        $application1 = DemonstratorApplication::factory()->create(['request_id' => $request1->id, 'is_accepted' => false]);
        $application2 = DemonstratorApplication::factory()->create(['request_id' => $request2->id, 'is_accepted' => true]);
        $staff->courses()->attach($courses);

        $this->assertTrue($application1->fresh()->isNew());
        $this->assertTrue($application2->fresh()->isNew());

        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application1->id));

        $response->assertStatus(200);
        $this->assertTrue($application1->fresh()->isAccepted());
        $this->assertFalse($application1->fresh()->isNew());
        Notification::assertSentTo($application1->student, AcademicAcceptsStudent::class);

        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application1->id));

        $response->assertStatus(200);
        $this->assertFalse($application1->fresh()->isAccepted());
        $this->assertFalse($application1->fresh()->isNew());

        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application2->id));

        $response->assertStatus(200);
        $this->assertFalse($application2->fresh()->isAccepted());
        $this->assertFalse($application2->fresh()->isNew());

        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application2->id));

        $response->assertStatus(200);
        $this->assertTrue($application2->fresh()->isAccepted());
        $this->assertFalse($application2->fresh()->isNew());
    }

    /** @test */
    public function staff_can_withdraw_a_request()
    {
        $staff = User::factory()->staff()->create();
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($staff)->postJson(route('request.withdraw', $request->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertCount(0, DemonstratorRequest::all());
    }

    /** @test */
    public function notification_is_not_sent_if_the_academic_has_quickly_changed_their_mind()
    {
        Queue::fake();
        $staff = User::factory()->staff()->create();
        $request1 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);
        $application1 = DemonstratorApplication::factory()->create(['request_id' => $request1->id, 'is_accepted' => false]);

        //on
        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application1->id));
        //off
        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application1->id));
        //on
        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application1->id));
        //off
        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application1->id));

        $response->assertStatus(200);
        $this->assertFalse($application1->fresh()->isAccepted());
        Queue::assertPushed(AcademicAcceptsStudentJob::class, function ($job) {
            return $job->shouldBeSkipped();
        });
    }

    /** @test */
    public function staff_can_withdraw_a_request_and_applied_students_are_notified()
    {
        Notification::fake();
        $staff = User::factory()->staff()->create();
        $student = User::factory()->student()->create();
        $student2 = User::factory()->student()->create();
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);

        $app1 = $student->applyFor($request);
        $app2 = $student2->applyFor($request);

        $response = $this->actingAs($staff)->postJson(route('request.withdraw', $request->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertCount(0, DemonstratorRequest::all());
        Notification::assertSentTo([$student, $student2], StudentRequestWithdrawn::class);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $app1->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $app2->id]);
    }

    /** @test */
    public function staff_cant_withdraw_a_request_with_accepted_applications()
    {
        Notification::fake();
        $staff = User::factory()->staff()->create();
        $application = DemonstratorApplication::factory()->create(['is_accepted' => true]);
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($staff)->postJson(route('request.withdraw', $request->id));

        $this->assertCount(1, DemonstratorRequest::all());
        Notification::assertNotSentTo([$application->student], StudentRequestWithdrawn::class);
    }

    /** @test */
    public function staff_can_disable_login_blurb()
    {
        $staff = User::factory()->staff()->create();
        $this->assertFalse($staff->hide_blurb);

        $response = $this->actingAs($staff)->post(route('user.disableBlurb', $staff));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($staff->fresh()->hide_blurb);
    }

    /** @test */
    public function we_can_send_an_academic_a_bundled_email_of_new_applications()
    {
        Notification::fake();
        $staff = User::factory()->staff()->create();
        $request1 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);
        $request2 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);
        $newApplications1 = DemonstratorApplication::factory()->count(3)->create(['request_id' => $request1->id]);
        $oldApplications1 = DemonstratorApplication::factory()->count(2)->create(['request_id' => $request1->id, 'is_new' => false]);
        $newApplications2 = DemonstratorApplication::factory()->count(5)->create(['request_id' => $request2->id]);
        $oldApplications2 = DemonstratorApplication::factory()->count(4)->create(['request_id' => $request2->id, 'is_new' => false]);

        $staff->sendNewApplicantsEmail();
        Notification::assertSentTo($staff, AcademicStudentsApplied::class, function ($notification, $channels) {
            return $notification->applications->count() == 8;
        });
    }

    /** @test */
    public function academic_is_not_emailed_if_no_new_applications_exist()
    {
        Notification::fake();
        $staff = User::factory()->staff()->create();
        $request1 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);
        $oldApplications1 = DemonstratorApplication::factory()->count(2)->create(['request_id' => $request1->id, 'is_new' => false]);

        $staff->sendNewApplicantsEmail();
        Notification::assertNotSentTo($staff, AcademicStudentsApplied::class);
    }

    /** @test */
    public function we_can_send_an_academic_a_bundled_email_of_new_confirmations()
    {
        Notification::fake();
        $staff = User::factory()->staff()->create();
        $request1 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);
        $request2 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);

        $confirmedApplications = DemonstratorApplication::factory()->create(['request_id' => $request1->id, 'student_confirms' => true, 'student_responded' => true]);
        $confirmedApplication2 = DemonstratorApplication::factory()->create(['request_id' => $request2->id, 'student_confirms' => true, 'student_responded' => true]);
        $declinedApplication1 = DemonstratorApplication::factory()->create(['request_id' => $request1->id, 'student_confirms' => false, 'student_responded' => true]);

        $unconfirmedApplication1 = DemonstratorApplication::factory()->create(['request_id' => $request1->id, 'student_responded' => false]);
        $unconfirmedApplication2 = DemonstratorApplication::factory()->create(['request_id' => $request2->id, 'student_responded' => false]);

        $applicationAlreadyNotified = DemonstratorApplication::factory()->create(['request_id' => $request1->id, 'student_confirms' => true, 'student_responded' => true, 'academic_notified' => true]);

        $this->assertEquals($staff->newConfirmations()->count(), 3);
        $this->assertEquals(DemonstratorApplication::count(), 6);

        $staff->sendNewConfirmationsEmail();
        Notification::assertSentTo($staff, AcademicStudentsConfirmation::class, function ($notification, $channels) {
            return $notification->applications->count() == 3;
        });

        $this->assertEquals(DemonstratorApplication::count(), 5);
        $this->assertEquals($staff->newConfirmations()->count(), 0);
    }

    /** @test */
    public function warning_is_not_displayed_if_a_request_has_a_start_date()
    {
        $staff = User::factory()->staff()->create();
        $request1 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id, 'start_date' => '2017-02-01']);
        $staff->courses()->attach($request1->course);

        $response = $this->actingAs($staff)->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee('notification');
    }

    /** @test */
    public function warning_is_displayed_if_a_request_has_no_start_date()
    {
        $staff = User::factory()->staff()->create();
        $request1 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id, 'start_date' => null]);
        $staff->courses()->attach($request1->course);

        $response = $this->actingAs($staff)->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('notification');
    }

    /** @test */
    public function demonstrator_request_can_have_a_required_degree_level()
    {
        $staff = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $degreeLevels = DegreeLevel::factory()->count(3)->create();
        $staff->courses()->attach($course);

        $request = DemonstratorRequest::factory()->make([
            'staff_id' => $staff->id,
            'course_id' => $course->id,
            'degree_levels' => [$degreeLevels[0]->id, $degreeLevels[1]->id],
            'start_date' => now()->format('d/m/Y'),
        ]);

        $response = $this->actingAs($staff)->postJson(route('request.update', $request->toArray()));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);

        $demRequest = DemonstratorRequest::first();
        $this->assertDatabaseHas('demonstrator_request_degree_levels', [
            'request_id' => $demRequest->id,
            'degree_level_id' => $degreeLevels[0]->id,
        ]);
        $this->assertDatabaseHas('demonstrator_request_degree_levels', [
            'request_id' => $demRequest->id,
            'degree_level_id' => $degreeLevels[1]->id,
        ]);
        $this->assertDatabaseMissing('demonstrator_request_degree_levels', [
            'request_id' => $demRequest->id,
            'degree_level_id' => $degreeLevels[2]->id,
        ]);
    }
}
