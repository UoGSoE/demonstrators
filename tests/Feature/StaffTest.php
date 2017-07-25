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

class StaffTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function staff_can_see_list_of_their_courses () {
        $staff = factory(User::class)->states('staff')->create();
        $courses = factory(Course::class, 3)->create();
        $staff->courses()->attach($courses);

        $response = $this->actingAs($staff)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($courses[0]->title);
        $response->assertSee($courses[1]->title);
        $response->assertSee($courses[2]->title);
    }

    /** @test */
    public function staff_can_submit_demonstrator_request_info () {
        $this->disableExceptionHandling();
        $staff = factory(User::class)->states('staff')->create();
        $course = factory(Course::class)->create();
        $staff->courses()->attach($course);

        $response = $this->actingAs($staff)->postJson(route('request.update', [
            'course_id' => $course->id,
            'hours_needed' => 10,
            'demonstrators_needed' => 2,
            'semester_1' => true,
            'semester_2' => true,
            'semester_3' => true,
            'skills' => 'Lasers',
        ]));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
    }

    /** @test */
    public function invalid_data_is_rejected () {
        $staff = factory(User::class)->states('staff')->create();
        $course = factory(Course::class)->create();
        $staff->courses()->attach($course);

        $response = $this->actingAs($staff)->postJson(route('request.update', [
            'course_id' => $course->id,
            'hours_needed' => 'Twelve',
            'demonstrators_needed' => 'Two',
            'semester_1' => 'Yes',
            'semester_2' => 'Yes',
            'semester_3' => 'Yes',
            'skills' => 'Lasers',
        ]));

        $response->assertStatus(422);
        $response->assertJsonStructure(['hours_needed', 'demonstrators_needed']);
    }

    /** @test */
    public function staff_can_see_demonstrator_applicants () {
        $staff = factory(User::class)->states('staff')->create();
        $courses = factory(Course::class, 3)->create();
        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[0]->id, 'staff_id' => $staff->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[1]->id, 'staff_id' => $staff->id]);
        $request3 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[2]->id, 'staff_id' => $staff->id]);
        $application1 = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id]);
        $application3 = factory(DemonstratorApplication::class)->create(['request_id' => $request3->id]);
        $staff->courses()->attach($courses);

        $response = $this->actingAs($staff)->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee($application1->student->surname);
        $response->assertSee($application2->student->surname);
        $response->assertSee($application3->student->surname);
    }

    /** @test */
    public function staff_can_toggle_accepted_status_on_applicants () {
        $staff = factory(User::class)->states('staff')->create();
        $courses = factory(Course::class, 3)->create();
        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[0]->id, 'staff_id' => $staff->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[1]->id, 'staff_id' => $staff->id]);
        $request3 = factory(DemonstratorRequest::class)->create(['course_id' => $courses[2]->id, 'staff_id' => $staff->id]);
        $application1 = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id, 'is_accepted' => false]);
        $application2 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id, 'is_accepted' => true]);
        $staff->courses()->attach($courses);

        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application1->id));

        $response->assertStatus(200);
        $this->assertTrue($application1->fresh()->isAccepted());

        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application1->id));

        $response->assertStatus(200);
        $this->assertFalse($application1->fresh()->isAccepted());

        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application2->id));

        $response->assertStatus(200);
        $this->assertFalse($application2->fresh()->isAccepted());

        $response = $this->actingAs($staff)->post(route('application.toggleaccepted', $application2->id));

        $response->assertStatus(200);
        $this->assertTrue($application2->fresh()->isAccepted());
    }
}