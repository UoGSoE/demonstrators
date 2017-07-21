<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Course;

class StaffTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function staff_can_make_requests_for_demonstrators_for_their_courses()
    {
        $staff = factory(User::class)->states('staff')->create();
        $course1 = factory(Course::class)->create();
        $course2 = factory(Course::class)->create();

        //

        $this->assertCount(3, $staff->requests);
        $this->assertCount(0, $staff->filledRequests);
        $this->assertCount(3, $staff->pendingRequests);
        $this->assertCount(2, $course1->pendingRequests);
        $this->assertCount(1, $course2->pendingRequests);
    }

    /** @test */
    public function staff_can_accept_requests_from_students()
    {
        $staff = factory(User::class)->states('staff')->create();
        $student1 = factory(User::class)->states('student')->create();
        $student2 = factory(User::class)->states('student')->create();
        $course1 = factory(Course::class)->create();
        $course2 = factory(Course::class)->create();
    }
}
