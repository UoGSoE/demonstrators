<?php

// @codingStandardsIgnoreFile

namespace Tests\Browser;

use App\Models\Course;
use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class StaffTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function staff_can_dismiss_the_blurb()
    {
        $this->browse(function (Browser $browser) {
            $staff = factory(User::class)->states('staff')->create(['hide_blurb' => false]);
            $browser->loginAs($staff)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests')
                ->assertSee('Welcome to the School of Engineering Teaching Assistants homepage.')
                ->click('#dismiss-blurb')
                ->waitUntilMissing('.modal');
        });
    }

    /** @test */
    public function staff_can_permanently_dismiss_the_blurb()
    {
        $this->browse(function (Browser $browser) {
            $staff = factory(User::class)->states('staff')->create(['hide_blurb' => false]);
            $browser->loginAs($staff)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests')
                ->assertSee('Welcome to the School of Engineering Teaching Assistants homepage.')
                ->click('.disable-blurb')
                ->waitUntilMissing('.modal')
                ->visit(route('home'))
                ->assertDontSee('Welcome to the School of Engineering Teaching Assistants homepage.');
            $this->assertDatabaseHas('users', ['id' => $staff->id, 'hide_blurb' => true]);
        });
    }

    /** @test */
    public function staff_can_make_blurb_appear_again()
    {
        $this->browse(function (Browser $browser) {
            $staff = factory(User::class)->states('staff')->create(['hide_blurb' => true]);
            $browser->loginAs($staff)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests')
                ->assertDontSee('Welcome to the School of Engineering Teaching Assistants homepage.')
                ->click('.toggle-blurb')
                ->waitFor('.modal')
                ->assertSee('Welcome to the School of Engineering Teaching Assistants homepage.');
        });
    }

    /** @test */
    public function staff_can_make_a_request()
    {
        $this->browse(function (Browser $browser) {
            $staff = factory(User::class)->states('staff')->create(['hide_blurb' => true]);
            $course = factory(Course::class)->create();
            $staff->courses()->attach($course->id);
            $browser->loginAs($staff)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests')
                ->assertSee($course->title)
                ->click('.flatpickr-input')
                ->click('.flatpickr-day')
                ->type('hours_needed', 12)
                ->type('hours_training', 6)
                ->type('demonstrators_needed', 3)
                ->check('semester_1')
                ->check('semester_2')
                ->type('skills', 'These are the skills')
                ->click('#submit-request')
                ->pause(2000);
            $this->assertDatabaseHas('demonstrator_requests', [
                'staff_id' => $staff->id,
                'course_id' => $course->id,
                'hours_needed' => 12,
                'hours_training' => 6,
                'demonstrators_needed' => 3,
                'semester_1' => true,
                'semester_2' => true,
                'skills' => 'These are the skills',
            ]);
        });
    }

    /** @test */
    public function staff_can_update_a_request()
    {
        $this->browse(function (Browser $browser) {
            $staff = factory(User::class)->states('staff')->create(['hide_blurb' => true]);
            $course = factory(Course::class)->create();
            $staff->courses()->attach($course->id);
            $request = factory(DemonstratorRequest::class)->create([
                'staff_id' => $staff->id,
                'course_id' => $course->id,
                'type' => 'Demonstrator',
                'hours_needed' => 7,
                'hours_training' => 8,
                'demonstrators_needed' => 2,
                'semester_1' => false,
                'semester_2' => false,
                'semester_3' => true,
            ]);
            $browser->loginAs($staff)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests')
                ->assertSee($course->title)
                ->type('hours_needed', 14)
                ->type('hours_training', 16)
                ->type('demonstrators_needed', 4)
                ->check('semester_1')
                ->type('skills', 'These are the skills')
                ->click('#submit-request')
                ->pause(1000);
            $this->assertDatabaseHas('demonstrator_requests', [
                'staff_id' => $staff->id,
                'course_id' => $course->id,
                'type' => 'Demonstrator',
                'hours_needed' => 14,
                'hours_training' => 16,
                'demonstrators_needed' => 4,
                'semester_1' => true,
                'semester_2' => false,
                'semester_3' => true,
                'skills' => 'These are the skills',
            ]);
            $this->assertDatabaseMissing('demonstrator_requests', [
                'staff_id' => $staff->id,
                'course_id' => $course->id,
                'type' => 'Demonstrator',
                'hours_needed' => 7,
                'hours_training' => 8,
                'demonstrators_needed' => 2,
                'semester_1' => false,
                'semester_2' => false,
                'semester_3' => true,
                'skills' => '',
            ]);
        });
    }

    /** @test */
    public function staff_can_delete_a_request()
    {
        $this->browse(function (Browser $browser) {
            $staff = factory(User::class)->states('staff')->create(['hide_blurb' => true]);
            $course = factory(Course::class)->create();
            $staff->courses()->attach($course->id);
            $request = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
            $browser->loginAs($staff)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests')
                ->assertSee($course->title)
                ->click('#withdraw-request')
                ->waitForText('DELETE - ARE YOU SURE?')
                ->click('#withdraw-request')
                ->pause(1000);
            $this->assertDatabaseMissing('demonstrator_requests', ['id' => $request->id]);
        });
    }

    /** @test */
    public function staff_cant_delete_a_request_with_accepted_applications()
    {
        $this->browse(function (Browser $browser) {
            $staff = factory(User::class)->states('staff')->create(['hide_blurb' => true]);
            $course = factory(Course::class)->create();
            $staff->courses()->attach($course->id);
            $request = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
            $application = factory(DemonstratorApplication::class)->create(['request_id' => $request->id, 'is_accepted' => true]);
            $browser->loginAs($staff)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests')
                ->assertSee($course->title)
                ->click('#withdraw-request')
                ->waitForText('DELETE - ARE YOU SURE?')
                ->click('#withdraw-request')
                ->waitForText('CANNOT DELETE - HAS ACCEPTED STUDENTS');
            $this->assertDatabaseHas('demonstrator_requests', ['id' => $request->id]);
        });
    }

    /** @test */
    public function staff_can_accept_a_student()
    {
        $this->browse(function (Browser $browser) {
            $staff = factory(User::class)->states('staff')->create(['hide_blurb' => true]);
            $course = factory(Course::class)->create();
            $staff->courses()->attach($course->id);
            $request = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
            $application = factory(DemonstratorApplication::class)->create(['request_id' => $request->id]);
            $browser->loginAs($staff)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests')
                ->assertSee($course->title)
                ->click('.applicants-tab')
                ->waitForText($application->student->fullName)
                ->click('.toggle-button')
                ->pause(1000);
            $this->assertDatabaseHas('demonstrator_applications', ['id' => $application->id, 'is_accepted' => true]);
        });
    }

    /** @test */
    public function staff_can_unaccept_a_student()
    {
        $this->browse(function (Browser $browser) {
            $staff = factory(User::class)->states('staff')->create(['hide_blurb' => true]);
            $course = factory(Course::class)->create();
            $staff->courses()->attach($course->id);
            $request = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
            $application = factory(DemonstratorApplication::class)->create(['request_id' => $request->id, 'is_accepted' => true]);
            $browser->loginAs($staff)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests')
                ->assertSee($course->title)
                ->click('.applicants-tab')
                ->waitForText($application->student->fullName)
                ->click('.toggle-button')
                ->pause(1000);
            $this->assertDatabaseHas('demonstrator_applications', ['id' => $application->id, 'is_accepted' => false]);
        });
    }
}
