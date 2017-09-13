<?php
// @codingStandardsIgnoreFile
namespace Tests\Unit;

use App\DemonstratorApplication;
use App\DemonstratorRequest;
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
    public function student_can_apply_for_demonstrator_request () {
        $student = factory(User::class)->states('student')->create();
        $demonstratorRequest = factory(DemonstratorRequest::class)->create();
        $application = $student->applyFor($demonstratorRequest);

        $this->assertEquals($application->student_id, $student->id);
        $this->assertEquals($application->request_id, $demonstratorRequest->id);
        $this->assertFalse($application->is_approved);
        $this->assertFalse($application->is_accepted);
    }

    /** @test */
    public function student_can_only_apply_for_a_request_once () {
        $student = factory(User::class)->states('student')->create();
        $demonstratorRequest = factory(DemonstratorRequest::class)->create();
        $application = $student->applyFor($demonstratorRequest);
        $this->assertEquals($application->student_id, $student->id);
        $this->assertEquals($application->request_id, $demonstratorRequest->id);
        $this->assertEquals(1, $demonstratorRequest->applications->count());

        $application = $student->applyFor($demonstratorRequest);
        $this->assertEquals(1, $demonstratorRequest->applications->count());
    }

    /** @test */
    public function student_can_withdraw_an_application () {
        $student = factory(User::class)->states('student')->create();
        $demonstratorRequest = factory(DemonstratorRequest::class)->create();

        $application = $student->applyFor($demonstratorRequest);
        $this->assertCount(1, $demonstratorRequest->applications);

        $student->withdraw($application);

        $this->assertCount(0, $demonstratorRequest->fresh()->applications);
    }

    /** @test */
    public function student_cant_withdraw_an_application_if_it_is_accepted () {
        Notification::fake();
        $student = factory(User::class)->states('student')->create();
        $demonstratorRequest = factory(DemonstratorRequest::class)->create();

        $application = $student->applyFor($demonstratorRequest);
        $application->toggleAccepted();
        $this->assertCount(1, $demonstratorRequest->applications);

        try {
            $student->withdraw($application);
        } catch(\Exception $e) {
            $this->assertCount(1, $demonstratorRequest->fresh()->applications);
            return;
        }
        $this->fail('Expected an exception to be thrown.');
    }

    /** @test */
    public function student_cant_withdraw_an_application_if_it_is_approved () {
        $student = factory(User::class)->states('student')->create();
        $demonstratorRequest = factory(DemonstratorRequest::class)->create();

        $application = $student->applyFor($demonstratorRequest);
        $application->approve();
        $this->assertCount(1, $demonstratorRequest->applications);

        try {
            $student->withdraw($application);
        } catch(\Exception $e) {
            $this->assertCount(1, $demonstratorRequest->fresh()->applications);
            return;
        }
        $this->fail('Expected an exception to be thrown.');
    }

    /** @test */
    public function can_check_if_request_has_application_from_a_student () {
        $student = factory(User::class)->states('student')->create();
        $demonstratorRequest = factory(DemonstratorRequest::class)->create();

        $application = $student->applyFor($demonstratorRequest);
        $this->assertEquals($demonstratorRequest->hasApplicationFrom($student), 1);
    }

    /** @test */
    public function student_can_confirm_their_acceptance () {
        Notification::fake();
        $student = factory(User::class)->states('student')->create();
        $demonstratorRequest = factory(DemonstratorRequest::class)->create();

        $application = $student->applyFor($demonstratorRequest);
        $application->toggleAccepted();
        $this->assertCount(1, $demonstratorRequest->applications);
    }
}