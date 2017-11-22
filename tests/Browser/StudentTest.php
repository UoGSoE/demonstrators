<?php
// @codingStandardsIgnoreFile
namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\DemonstratorRequest;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class StudentTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function student_can_apply_for_a_request()
    {
        $this->browse(function (Browser $browser) { 
            $student = factory(User::class)->states('student')->create(['hide_blurb' => true]);
            $request = factory(DemonstratorRequest::class)->create();
            $request->staff->courses()->attach($request->course);
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests')
                ->assertSee($request->course->title)
                ->assertSee($request->staff->fullName)
                ->assertSee($request->type)
                ->assertSee('APPLY')
                ->clickLink('Apply')
                ->waitForText('WITHDRAW');
            $this->assertDatabaseHas('demonstrator_applications', ['student_id' => $student->id]);
        });
    }
}
