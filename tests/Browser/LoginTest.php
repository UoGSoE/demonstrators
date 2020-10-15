<?php

// @codingStandardsIgnoreFile

namespace Tests\Browser;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function student_can_login()
    {
        $student = factory(User::class)->states('student')->create();
        $this->browse(function (Browser $browser) use ($student) {
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests');
        });
    }

    /** @test */
    public function staff_can_login()
    {
        $student = factory(User::class)->states('staff')->create();
        $this->browse(function (Browser $browser) use ($student) {
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Teaching Assistant Requests');
        });
    }
}
