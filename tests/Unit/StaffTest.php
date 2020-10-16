<?php

// @codingStandardsIgnoreFile

namespace Tests\Unit;

use App\Models\Course;
use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StaffTest extends TestCase
{
    /** @test */
    public function staff_can_make_requests_for_demonstrators()
    {
        $staff = User::factory()->staff()->create();
        $course1 = Course::factory()->create();
        $course2 = Course::factory()->create();

        $demonstratorRequest1 = $staff->requestDemonstrators([
            'degree_levels' => null,
            'course_id' => $course1->id,
            'start_date' => (new Carbon('next month'))->format('Y-m-d'),
            'hours_needed' => 20,
            'demonstrators_needed' => 2,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
        ]);
        $demonstratorRequest2 = $staff->requestDemonstrators([
            'degree_levels' => null,
            'course_id' => $course2->id,
            'start_date' => (new Carbon('next month'))->format('Y-m-d'),
            'hours_needed' => 30,
            'demonstrators_needed' => 3,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Tutor',
        ]);

        $this->assertCount(2, $staff->requests);
        $this->assertCount(1, $course1->requests);
        $this->assertCount(1, $course2->requests);
        tap($demonstratorRequest1, function ($req) use ($course1, $staff) {
            $this->assertEquals($course1->id, $req->course_id);
            $this->assertEquals(20, $req->hours_needed);
            $this->assertEquals(2, $req->demonstrators_needed);
            $this->assertEquals('Lasers', $req->skills);
            $this->assertEquals($staff->id, $req->staff_id);
        });

        tap($demonstratorRequest2, function ($req) use ($course2, $staff) {
            $this->assertEquals($course2->id, $req->course_id);
            $this->assertEquals(30, $req->hours_needed);
            $this->assertEquals(3, $req->demonstrators_needed);
            $this->assertEquals('Lasers', $req->skills);
            $this->assertEquals($staff->id, $req->staff_id);
        });
    }

    /** @test */
    public function staff_can_accept_requests_from_students()
    {
        Notification::fake();
        $staff = User::factory()->staff()->create();
        $student1 = User::factory()->student()->create();
        $student2 = User::factory()->student()->create();
        $course1 = Course::factory()->create();
        $course2 = Course::factory()->create();
        $demonstratorRequest = $staff->requestDemonstrators([
            'degree_levels' => null,
            'course_id' => $course1->id,
            'start_date' => (new Carbon('next month'))->format('Y-m-d'),
            'hours_needed' => 20,
            'demonstrators_needed' => 2,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
        ]);
        $application = $student1->applyFor($demonstratorRequest);
        $this->assertFalse($application->isAccepted());

        $application->toggleAccepted();

        $this->assertTrue($application->fresh()->isAccepted());
    }

    /** @test */
    public function staff_can_update_a_request()
    {
        $staff = User::factory()->staff()->create();
        $course1 = Course::factory()->create();

        $demonstratorRequest1 = $staff->requestDemonstrators([
            'degree_levels' => null,
            'course_id' => $course1->id,
            'start_date' => (new Carbon('next month'))->format('Y-m-d'),
            'hours_needed' => 20,
            'demonstrators_needed' => 2,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
        ]);
        $demonstratorRequest2 = $staff->requestDemonstrators([
            'degree_levels' => null,
            'course_id' => $course1->id,
            'start_date' => (new Carbon('next month'))->format('Y-m-d'),
            'hours_needed' => 30,
            'demonstrators_needed' => 3,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
        ]);

        $request = $staff->requests->first();
        $this->assertEquals(30, $request->hours_needed);
        $this->assertEquals(3, $request->demonstrators_needed);
    }

    /** @test */
    public function staff_can_only_have_one_request_of_a_given_type_per_course()
    {
        $staff = User::factory()->staff()->create();
        $course1 = Course::factory()->create();

        $demonstratorRequest1 = $staff->requestDemonstrators([
            'degree_levels' => null,
            'course_id' => $course1->id,
            'start_date' => (new Carbon('next month'))->format('Y-m-d'),
            'hours_needed' => 20,
            'demonstrators_needed' => 2,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
        ]);
        $demonstratorRequest2 = $staff->requestDemonstrators([
            'degree_levels' => null,
            'course_id' => $course1->id,
            'start_date' => (new Carbon('next month'))->format('Y-m-d'),
            'hours_needed' => 30,
            'demonstrators_needed' => 3,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
        ]);

        $this->assertCount(1, $staff->requests);
    }

    /** @test */
    public function staff_cant_edit_a_request_if_there_are_any_accepted_applications()
    {
        Notification::fake();
        $staff = User::factory()->staff()->create();
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();

        $demonstratorRequest = $staff->requestDemonstrators([
            'degree_levels' => null,
            'course_id' => $course->id,
            'start_date' => (new Carbon('next month'))->format('Y-m-d'),
            'hours_needed' => 20,
            'demonstrators_needed' => 2,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
        ]);

        $application = $student->applyFor($demonstratorRequest);
        $application->toggleAccepted();

        $request = $staff->requestDemonstrators([
            'degree_levels' => null,
            'course_id' => $course->id,
            'start_date' => (new Carbon('next month'))->format('Y-m-d'),
            'hours_needed' => 30,
            'demonstrators_needed' => 2,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
            'type' => 'Demonstrator',
        ]);

        $this->assertFalse($request);

        $request = $staff->requests->first();
        $this->assertEquals(20, $request->hours_needed);
    }

    /** @test */
    public function staff_can_delete_their_request()
    {
        $staff = User::factory()->staff()->create();
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);

        $this->assertCount(1, $staff->requests);

        $staff->withdrawRequest($request);

        $this->assertCount(0, $staff->fresh()->requests);
    }

    /** @test */
    public function we_can_find_all_new_student_applications_for_a_member_of_staff()
    {
        $staff = User::factory()->staff()->create();
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);
        $newApplications = DemonstratorApplication::factory()->count(3)->create(['request_id' => $request->id]);
        $oldApplications = DemonstratorApplication::factory()->count(2)->create(['request_id' => $request->id, 'is_new' => false]);

        $this->assertCount(3, $staff->newApplications());
    }

    /** @test */
    public function staff_can_dismiss_the_login_message_permanently()
    {
        $staff = User::factory()->staff()->create();

        $this->assertFalse($staff->hide_blurb);

        $staff->disableBlurb();

        $this->assertTrue($staff->fresh()->hide_blurb);
    }

    /** @test */
    public function can_attach_a_course_to_an_academic()
    {
        $staff = User::factory()->staff()->create();
        $course1 = Course::factory()->create();
        $this->assertCount(0, $staff->courses);

        $staff->addToCourse($course1->id);

        $this->assertCount(1, $staff->fresh()->courses);
    }

    /** @test */
    public function can_remove_a_course_from_an_academic()
    {
        $staff = User::factory()->staff()->create();
        $course1 = Course::factory()->create();
        $course1->staff()->attach($staff);
        $this->assertCount(1, $staff->courses);

        $staff->removeFromCourse($course1->id);

        $this->assertCount(0, $staff->fresh()->courses);
    }

    /** @test */
    public function can_mark_applications_for_a_course_as_seen()
    {
        $staff = User::factory()->staff()->create();
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);
        $newApplications = DemonstratorApplication::factory()->count(3)->create(['request_id' => $request->id, 'academic_seen' => false]);

        $this->actingAs($staff);
        $staff->markApplicationsSeen($request->course);

        $this->assertTrue($newApplications[0]->fresh()->academic_seen);
        $this->assertTrue($newApplications[1]->fresh()->academic_seen);
        $this->assertTrue($newApplications[2]->fresh()->academic_seen);
    }

    /** @test */
    public function admin_cant_mark_other_staff_applications_for_a_course_as_seen()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);
        $newApplications = DemonstratorApplication::factory()->count(3)->create(['request_id' => $request->id, 'academic_seen' => false]);

        $this->actingAs($admin);
        $staff->markApplicationsSeen($request->course);

        $this->assertFalse($newApplications[0]->fresh()->academic_seen);
        $this->assertFalse($newApplications[1]->fresh()->academic_seen);
        $this->assertFalse($newApplications[2]->fresh()->academic_seen);
    }
}
