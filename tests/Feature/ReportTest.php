<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class ReportTest extends TestCase
{
    /** @test */
    public function test_output_1()
    {
        $admin = User::factory()->admin()->create();
        $applications = DemonstratorApplication::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.output1'));

        $response->assertStatus(200);
        $response->assertViewHas('courses');
        $this->assertCount(2, $response->data('courses'));
    }

    /** @test */
    public function test_output_2()
    {
        $admin = User::factory()->admin()->create();
        $applications = DemonstratorApplication::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.output2'));

        $response->assertStatus(200);
        $response->assertViewHas('courses');
        $this->assertCount(2, $response->data('courses'));
    }

    /** @test */
    public function test_output_3()
    {
        $admin = User::factory()->admin()->create();
        $confirmedApplications = DemonstratorApplication::factory()->count(2)->create(['is_accepted' => true, 'student_responded' => true, 'student_confirms' => true]);
        $unconfirmedApplication = DemonstratorApplication::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.output3'));

        $response->assertStatus(200);
        $response->assertViewHas('students');
        $this->assertCount($confirmedApplications->count(), $response->data('students'));
    }

    /** @test */
    public function test_output_4()
    {
        $admin = User::factory()->admin()->create();
        $confirmedApplications = DemonstratorApplication::factory()->count(2)->create(['is_accepted' => true]);
        $unconfirmedApplication = DemonstratorApplication::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.output4'));

        $response->assertStatus(200);
        $response->assertViewHas('courses');
        $this->assertCount($confirmedApplications->count(), $response->data('courses'));
    }

    /** @test */
    public function test_output_5()
    {
        $admin = User::factory()->admin()->create();
        $requestsWithoutApplications = DemonstratorRequest::factory()->count(4)->create();
        $applications = DemonstratorApplication::factory()->count(2)->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.output5'));

        $response->assertStatus(200);
        $response->assertViewHas('requests');
        $this->assertCount($requestsWithoutApplications->count(), $response->data('requests'));
    }

    /** @test */
    public function test_output_6()
    {
        $admin = User::factory()->admin()->create();
        $neglectedApplications = DemonstratorApplication::factory()->count(2)->create(['created_at' => new Carbon('4 days ago'), 'academic_seen' => false]);
        $seenApplication = DemonstratorApplication::factory()->count(3)->create(['created_at' => new Carbon('4 days ago'), 'academic_seen' => true]);

        $response = $this->actingAs($admin)->get(route('admin.reports.output6'));

        $response->assertStatus(200);
        $response->assertViewHas('applications');
        $response->assertSee($neglectedApplications[0]->request->course->title);
        $response->assertSee($neglectedApplications[1]->request->course->title);
        $response->assertDontSee($seenApplication[0]->request->course->title);
        $response->assertDontSee($seenApplication[1]->request->course->title);
        $response->assertDontSee($seenApplication[2]->request->course->title);
        $this->assertCount($neglectedApplications->count(), $response->data('applications'));
    }

    /** @test */
    public function test_output_7()
    {
        $admin = User::factory()->admin()->create();
        $unacceptedApplications = DemonstratorApplication::factory()->count(4)->create(['is_accepted' => false]);
        $acceptedApplications = DemonstratorApplication::factory()->count(2)->create(['is_accepted' => true]);

        $response = $this->actingAs($admin)->get(route('admin.reports.output7'));

        $response->assertStatus(200);
        $response->assertViewHas('applications');
        $this->assertCount($unacceptedApplications->count(), $response->data('applications'));
    }
}
