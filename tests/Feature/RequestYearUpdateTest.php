<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\DemonstratorApplication;
use App\DemonstratorRequest;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class RequestYearUpdateTest extends TestCase
{
    /** @test */
    public function admin_can_change_all_requests_start_dates_to_next_academic_year()
    {
        $admin = factory(User::class)->states('admin')->create();
        $requests = create(DemonstratorRequest::class, [], 3);

        $response = $this->actingAs($admin)->post(route('admin.requests.update_year'));

        $response->assertStatus(302);
        $response->assertRedirect('/');

        foreach ($requests as $request) {
            $this->assertEquals(Carbon::parse($request->start_date)->addWeeks(52)->format('Y-m-d'), $request->fresh()->start_date);
        }
    }

    /** @test */
    public function admin_cant_change_all_requests_start_dates_to_next_academic_year_if_any_have_applications()
    {
        $admin = factory(User::class)->states('admin')->create();
        $requests = create(DemonstratorRequest::class, [], 3);
        $application = create(DemonstratorApplication::class);

        $response = $this->actingAs($admin)->post(route('admin.requests.update_year'));

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $response->assertSessionHasErrors('applications');

        foreach ($requests as $request) {
            $this->assertEquals($request->start_date, $request->fresh()->start_date);
        }
        $this->assertEquals($application->request->start_date, $application->request->fresh()->start_date);
    }
}
