<?php
// @codingStandardsIgnoreFile
namespace Tests\Browser;

use App\User;
use App\Course;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

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

    // /** @test */
    // public function staff_can_make_a_request ()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $staff = factory(User::class)->states('staff')->create(['hide_blurb' => true]);
    //         $course = factory(Course::class)->create();
    //         $staff->courses()->attach($course->id);
    //         $browser->loginAs($staff)
    //             ->visit(route('home'))
    //             ->assertSee('School of Engineering - Teaching Assistants')
    //             ->assertSee('Teaching Assistant Requests')
    //             ->assertSee($course->title)
    //             ->type('start_date', '10/11/1992')
    //             ->type('hours_needed', 12)
    //             ->type('hours_training', 6)
    //             ->type('demonstrators_needed', 3)
    //             ->check('semester_1')
    //             ->check('semester_2')
    //             ->type('skills', 'These are the skills')
    //             ->click('#submit-request');
    //         $this->assertDatabaseHas('demonstrator_request', [
    //             'staff_id' => $staff->id,
    //             'course_id' => $course->id,
    //             'start_date' => '1992-11-10',
    //             'hours_needed' => 12,
    //             'hours_training' => 6,
    //             'demonstrators_needed' => 3,
    //             'semester_1' => true,
    //             'semester_2' => true,
    //             'skills' => 'These are the skills',
    //         ]);
    //     });
    // }
}
