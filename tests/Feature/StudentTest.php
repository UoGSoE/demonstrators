<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\Course;
use App\DemonstratorApplication;
use App\DemonstratorRequest;
use App\Notifications\StudentConfirmWithContract;
use App\Notifications\StudentRTWInfo;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function student_can_see_list_of_requests () {
        $student = factory(User::class)->states('student')->create();
        $courses = factory(Course::class, 3)->create();
        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[0]->id, 'hours_training' => 555]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[1]->id]);

        $response = $this->actingAs($student)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($request1->course->title);
        $response->assertSee((string)$request1->hours_needed);
        $response->assertSee((string)$request1->hours_training);
        $response->assertSee($request2->course->title);
        $response->assertSee((string)$request2->hours_needed);
    }

    /** @test */
    public function student_can_apply_for_a_request () {
        $student = factory(User::class)->states('student')->create();
        $request = factory(DemonstratorRequest::class)->create();
        $this->disableExceptionHandling();

        $response = $this->actingAs($student)->post(route('application.apply', $request->id), ['hours' => 2]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $application = DemonstratorApplication::first();
        $this->assertEquals($student->id, $application->student_id);
        $this->assertEquals($request->id, $application->request_id);
    }

    /** @test */
    public function student_can_change_their_notes () {
        $student = factory(User::class)->states('student')->create();
        $request = factory(DemonstratorRequest::class)->create();

        $response = $this->actingAs($student)->post(route('student.notes', $student), ['notes' => 'This is my notes.']);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertEquals($student->fresh()->notes, 'This is my notes.');
    }

    /** @test */
    public function student_can_unapply_for_a_request () {
        $application = factory(DemonstratorApplication::class)->create();
        $application2 = factory(DemonstratorApplication::class)->create();

        $response = $this->actingAs($application->student)->post(route('application.destroy', $application->request_id), ['withdraw' => true]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertEquals(1, DemonstratorApplication::count());
    }

    /** @test */
    public function student_cant_see_requests_that_have_accepted_max_students () {
        $student = factory(User::class)->states('student')->create();
        $request = factory(DemonstratorRequest::class)->create(['demonstrators_needed' => 1]);
        $request2 = factory(DemonstratorRequest::class)->create(['demonstrators_needed' => 1]);
        $application = factory(DemonstratorApplication::class)->create(['is_accepted' => 1, 'request_id' => $request->id]);

        $response = $this->actingAs($student)->get(route('home'));

        $response->assertStatus(200);
        $response->assertDontSee($request->course->title);
        $response->assertSee($request2->course->title);
        $response->assertSee((string)$request2->hours_needed);
    }

    /** @test */
    public function students_can_see_their_acceptance_and_can_confirm_or_decline () {
        $application = factory(DemonstratorApplication::class)->create(['is_accepted' => true]);
        $application2 = factory(DemonstratorApplication::class)->create(['is_accepted' => false, 'student_responded' => false]);

        $response = $this->actingAs($application->student)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Accepted Applications');
        $response->assertSee($application->request->course->title);
        $response->assertDontSee("$application2->request->course->code $application2->request->course->title");
    }

    /** @test */
    public function students_can_confirm_their_acceptance () {
        Notification::fake();
        $application = factory(DemonstratorApplication::class)->create();

        $response = $this->actingAs($application->student)->post(route('application.studentconfirms', $application->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($application->fresh()->student_confirms);
        Notification::assertSentTo($application->student, StudentRTWInfo::class);
    }

    /** @test */
    public function students_can_confirm_their_acceptance_that_already_has_a_contract () {
        Notification::fake();
        $student = factory(User::class)->create(['has_contract' => true]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);

        $response = $this->actingAs($student)->post(route('application.studentconfirms', $application->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($application->fresh()->student_confirms);
        Notification::assertSentTo($application->student, StudentConfirmWithContract::class);
    }


    /** @test */
    public function students_can_decline_the_position () {
        $application = factory(DemonstratorApplication::class)->create();

        $response = $this->actingAs($application->student)->post(route('application.studentdeclines', $application->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
    }

    /** @test */
    public function students_can_confirm_their_acceptance_for_two_but_only_emailed_about_rtw_once () {
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
}