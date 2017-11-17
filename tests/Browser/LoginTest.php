<?php
// @codingStandardsIgnoreFile
namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends DuskTestCase
{
    use RefreshDatabase;

    /** @test */
    public function student_can_login()
    {
        $student = factory(User::class)->states('student')->create();
        $this->browse(function (Browser $browser) use ($student) {
            $browser->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Login')
                ->type('username', $student->username)
                ->type('password', bcrypt($student->password));
        });
    }
}
