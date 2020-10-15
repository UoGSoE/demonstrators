<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    /** @test */
    public function a_reminder_is_sent_to_staff_who_havent_viewed_applications_after_three_days()
    {
        Notification::fake();
        $application = factory(\App\Models\DemonstratorApplication::class)->create(['created_at' => new Carbon('Last week'), 'academic_seen' => false]);

        $application->request->staff->notifyAboutOutstandingRequests();

        Notification::assertSentTo($application->request->staff, \App\Notifications\NeglectedRequests::class);
        $this->assertTrue($application->request->fresh()->reminder_sent);
    }

    /** @test */
    public function a_reminder_isnt_sent_to_staff_if_one_has_already_been_sent()
    {
        Notification::fake();
        $request = factory(\App\Models\DemonstratorRequest::class)->create(['reminder_sent' => true]);
        $application = factory(\App\Models\DemonstratorApplication::class)->create(['request_id' => $request->id, 'created_at' => new Carbon('Last week')]);

        $application->request->staff->notifyAboutOutstandingRequests();

        Notification::assertNotSentTo($application->request->staff, \App\Notifications\NeglectedRequests::class);
    }

    /** @test */
    public function a_reminder_isnt_sent_to_staff_if_all_applications_are_seen()
    {
        Notification::fake();
        $application = factory(\App\Models\DemonstratorApplication::class)->create(['created_at' => new Carbon('Last week'), 'academic_seen' => true]);

        $application->request->staff->notifyAboutOutstandingRequests();

        Notification::assertNotSentTo($application->request->staff, \App\Notifications\NeglectedRequests::class);
    }

    /** @test */
    public function a_reminder_isnt_sent_to_staff_if_there_are_no_applications_older_than_three_days()
    {
        Notification::fake();
        $application = factory(\App\Models\DemonstratorApplication::class)->create(['created_at' => new Carbon('2 days ago')]);

        $application->request->staff->notifyAboutOutstandingRequests();

        Notification::assertNotSentTo($application->request->staff, \App\Notifications\NeglectedRequests::class);
    }

    /** @test */
    public function a_reminder_bundles_all_outstanding_requests()
    {
        Notification::fake();
        $application = factory(\App\Models\DemonstratorApplication::class)->create(['created_at' => new Carbon('Last week')]);
        $application2 = factory(\App\Models\DemonstratorApplication::class)->create(['request_id' => $application->request->id, 'created_at' => new Carbon('Last week')]);
        $application3 = factory(\App\Models\DemonstratorApplication::class)->create(['request_id' => $application->request->id, 'created_at' => new Carbon('1 day ago')]);

        $request = factory(\App\Models\DemonstratorRequest::class)->create(['staff_id' => $application->request->staff_id]);
        $application = factory(\App\Models\DemonstratorApplication::class)->create(['request_id' => $request->id, 'created_at' => new Carbon('Last week')]);

        $application->request->staff->notifyAboutOutstandingRequests();

        Notification::assertSentTo($application->request->staff, \App\Notifications\NeglectedRequests::class, function ($notification, $channels) {
            return $notification->requests->count() == 2;
        });
    }
}
