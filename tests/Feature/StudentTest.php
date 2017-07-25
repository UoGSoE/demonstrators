<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\Course;
use App\DemonstratorApplication;
use App\DemonstratorRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function student_can_see_list_of_requests () {
        $student = factory(User::class)->states('student')->create();
        $courses = factory(Course::class, 3)->create();
        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[0]->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[1]->id]);

        $response = $this->actingAs($student)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($request1->course->title);
        $response->assertSee((string)$request1->hours_needed);
        $response->assertSee($request2->course->title);
        $response->assertSee((string)$request2->hours_needed);
    }

    /** @test */
    public function student_can_apply_for_a_request () {
        $student = factory(User::class)->states('student')->create();
        $request = factory(DemonstratorRequest::class)->create();

        $response = $this->actingAs($student)->post(route('request.apply', $request->id));

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
}