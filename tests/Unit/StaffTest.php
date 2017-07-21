<?php
// @codingStandardsIgnoreFile

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Course;
use Carbon\Carbon;

class StaffTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function staff_can_make_requests_for_demonstrators()
    {
        $staff = factory(User::class)->states('staff')->create();
        $course1 = factory(Course::class)->create();
        $course2 = factory(Course::class)->create();

        $demonstratorRequest1 = $staff->requestDemonstrators([
            'course_id' => $course1->id,
            'hours_needed' => 20,
            'demonstrators_needed' => 2,
            'starting' => Carbon::now()->subMonths(2),
            'ending' => Carbon::now()->addMonths(2),
            'skills' => 'Lasers'
        ]);
        $demonstratorRequest2 = $staff->requestDemonstrators([
            'course_id' => $course2->id,
            'hours_needed' => 30,
            'demonstrators_needed' => 3,
            'starting' => Carbon::now()->subMonths(2),
            'ending' => Carbon::now()->addMonths(2),
            'skills' => 'Lasers'
        ]);

        $this->assertCount(2, $staff->requests);
        $this->assertCount(1, $course1->requests);
        $this->assertCount(1, $course2->requests);
        tap($demonstratorRequest1, function($req) use ($course1, $staff) {
            $this->assertEquals($course1->id, $req->course_id);
            $this->assertEquals(20, $req->hours_needed);
            $this->assertEquals(2, $req->demonstrators_needed);
            $this->assertEquals('Lasers', $req->skills);
            $this->assertEquals($staff->id, $req->staff_id);
            $this->assertEquals(Carbon::now()->subMonths(2)->format('Y-m-d 00:00:00'), $req->starting);
            $this->assertEquals(Carbon::now()->addMonths(2)->format('Y-m-d 00:00:00'), $req->ending);
        });

        tap($demonstratorRequest2, function($req) use ($course2, $staff) {
            $this->assertEquals($course2->id, $req->course_id);
            $this->assertEquals(30, $req->hours_needed);
            $this->assertEquals(3, $req->demonstrators_needed);
            $this->assertEquals('Lasers', $req->skills);
            $this->assertEquals($staff->id, $req->staff_id);
            $this->assertEquals(Carbon::now()->subMonths(2)->format('Y-m-d 00:00:00'), $req->starting);
            $this->assertEquals(Carbon::now()->addMonths(2)->format('Y-m-d 00:00:00'), $req->ending);
        });
    }

    /** @test */
    public function staff_can_accept_requests_from_students()
    {
        $staff = factory(User::class)->states('staff')->create();
        $student1 = factory(User::class)->states('student')->create();
        $student2 = factory(User::class)->states('student')->create();
        $course1 = factory(Course::class)->create();
        $course2 = factory(Course::class)->create();
        $demonstratorRequest = $staff->requestDemonstrators([
            'course_id' => $course1->id,
            'hours_needed' => 20,
            'demonstrators_needed' => 2,
            'starting' => Carbon::now()->subMonths(2),
            'ending' => Carbon::now()->addMonths(2),
            'skills' => 'Lasers'
        ]);
        $application = $student1->applyFor($demonstratorRequest);
        $this->assertFalse($application->isAccepted());

        $staff->accept($application);

        $this->assertTrue($application->fresh()->isAccepted());
    }

    /** @test */
    public function there_can_only_be_one_demonstrator_request_per_course()
    {
        $staff = factory(User::class)->states('staff')->create();
        $course1 = factory(Course::class)->create();

        $demonstratorRequest1 = $staff->requestDemonstrators([
            'course_id' => $course1->id,
            'hours_needed' => 20,
            'demonstrators_needed' => 2,
            'starting' => Carbon::now()->subMonths(2),
            'ending' => Carbon::now()->addMonths(2),
            'skills' => 'Lasers'
        ]);
        $demonstratorRequest2 = $staff->requestDemonstrators([
            'course_id' => $course1->id,
            'hours_needed' => 30,
            'demonstrators_needed' => 3,
            'starting' => Carbon::now()->subMonths(2),
            'ending' => Carbon::now()->addMonths(2),
            'skills' => 'Lasers'
        ]);

        $this->assertCount(1, $staff->requests);
    }
}
