<?php

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
    public function staff_can_make_requests_for_demonstrators_for_their_courses()
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
        $this->assertCount(0, $staff->filledRequests);
        $this->assertCount(2, $staff->pendingRequests);
        $this->assertCount(1, $course1->pendingRequests);
        $this->assertCount(0, $course1->filledRequests);
        $this->assertCount(1, $course2->pendingRequests);
        $this->assertCount(0, $course2->filledRequests);
        $this->assertEquals([
            'course_id' => $course1->id,
            'hours_needed' => 20,
            'demonstrators_needed' => 2,
            'starting' => Carbon::now()->subMonths(2),
            'ending' => Carbon::now()->addMonths(2),
            'skills' => 'Lasers',
            'is_filled' => false,
        ], $demonstratorRequest1->toArray());
        $this->assertEquals([
            'course_id' => $course2->id,
            'hours_needed' => 30,
            'demonstrators_needed' => 3,
            'starting' => Carbon::now()->subMonths(2),
            'ending' => Carbon::now()->addMonths(2),
            'skills' => 'Lasers',
            'is_filled' => false,
        ], $demonstratorRequest2->toArray());
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

        $staff->accept($application);

        $this->assertCount(1, $staff->requests);
        $this->assertCount(1, $staff->filledRequests);
        $this->assertCount(0, $staff->pendingRequests);
        $this->assertCount(0, $course1->pendingRequests);
        $this->assertCount(1, $course1->filledRequests);
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
