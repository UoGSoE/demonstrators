<?php
// @codingStandardsIgnoreFile
namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\DemonstratorRequest;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\DemonstratorApplication;

class StudentTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function student_can_dismiss_the_blurb ()
    {
        $this->browse(function (Browser $browser) {
            $student = factory(User::class)->states('student')->create(['hide_blurb' => false]);
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests')
                ->assertSee('Welcome to the School of Engineering Teaching Assistant Pages.')
                ->click('#dismiss-blurb')
                ->waitUntilMissing('.modal');
        });
    }

    /** @test */
    public function student_can_permanently_dismiss_the_blurb()
    {
        $this->browse(function (Browser $browser) {
            $student = factory(User::class)->states('student')->create(['hide_blurb' => false]);
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests')
                ->assertSee('Welcome to the School of Engineering Teaching Assistant Pages.')
                ->click('.disable-blurb')
                ->waitUntilMissing('.modal')
                ->visit(route('home'))
                ->assertDontSee('Welcome to the School of Engineering Teaching Assistant Pages.');
            $this->assertDatabaseHas('users', ['id' => $student->id, 'hide_blurb' => true]);
        });
    }

    /** @test */
    public function student_can_make_blurb_appear_again ()
    {
        $this->browse(function (Browser $browser) {
            $student = factory(User::class)->states('student')->create(['hide_blurb' => true]);
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests')
                ->assertDontSee('Welcome to the School of Engineering Teaching Assistant Pages.')
                ->click('.toggle-blurb')
                ->waitFor('.modal')
                ->assertSee('Welcome to the School of Engineering Teaching Assistant Pages.');
        });
    }

    /** @test */
    public function student_can_add_notes_about_themselves ()
    {
        $this->browse(function (Browser $browser) {
            $student = factory(User::class)->states('student')->create(['hide_blurb' => true]);
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests')
                ->click('#info-button')
                ->waitFor('.notes')
                ->type('notes', 'ABCDEFG')
                ->click('.notes-button')
                ->waitUntilMissing('.notes');
            $this->assertEquals($student->fresh()->notes, 'ABCDEFG');
        });
    }

    /** @test */
    public function student_can_update_notes_about_themselves()
    {
        $this->browse(function (Browser $browser) {
            $student = factory(User::class)->states('student')->create(['hide_blurb' => true, 'notes' => 'This is my note.']);
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests')
                ->click('#info-button')
                ->waitFor('.notes')
                ->assertInputValue('notes', $student->notes)
                ->type('notes', 'ABCDEFG')
                ->click('.notes-button')
                ->waitUntilMissing('.notes');
            $this->assertEquals($student->fresh()->notes, 'ABCDEFG');
        });
    }

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

    /** @test */
    public function student_can_withdraw_from_a_request ()
    {
        $this->browse(function (Browser $browser) { 
            $student = factory(User::class)->states('student')->create(['hide_blurb' => true]);
            $request = factory(DemonstratorRequest::class)->create();
            $request->staff->courses()->attach($request->course);
            $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id, 'request_id' => $request->id]);
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests')
                ->assertSee($request->course->title)
                ->assertSee($request->staff->fullName)
                ->assertSee($request->type)
                ->assertSee('WITHDRAW')
                ->clickLink('Withdraw')
                ->waitForText('APPLY');
            $this->assertDatabaseMissing('demonstrator_applications', ['student_id' => $student->id]);
        });
    }

    /** @test */
    public function student_can_accept_a_job_offer ()
    {
        $this->browse(function (Browser $browser) {
            $student = factory(User::class)->states('student')->create(['hide_blurb' => true]);
            $request = factory(DemonstratorRequest::class)->create();
            $request->staff->courses()->attach($request->course);
            $application = factory(DemonstratorApplication::class)->create([
                'student_id' => $student->id,
                'request_id' => $request->id,
                'is_accepted' => true,
            ]);
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests')
                ->assertSee('You have been accepted for a position.')
                ->assertSee($request->course->title)
                ->assertSee($request->staff->fullName)
                ->assertSee($request->type)
                ->assertSee('ACCEPT')
                ->clickLink('Accept')
                ->waitUntilMissing('ACCEPT');
            $this->assertDatabaseHas('demonstrator_applications', [
                'student_id' => $student->id,
                'request_id' => $request->id,
                'is_accepted' => true,
                'student_responded' => true,
                'student_confirms' => true,
            ]);
        });
    }

    /** @test */
    public function student_can_decline_a_job_offer()
    {
        $this->browse(function (Browser $browser) {
            $student = factory(User::class)->states('student')->create(['hide_blurb' => true]);
            $request = factory(DemonstratorRequest::class)->create();
            $request->staff->courses()->attach($request->course);
            $application = factory(DemonstratorApplication::class)->create([
                'student_id' => $student->id,
                'request_id' => $request->id,
                'is_accepted' => true,
            ]);
            $browser->loginAs($student)
                ->visit(route('home'))
                ->assertSee('School of Engineering - Teaching Assistants')
                ->assertSee('Available Requests')
                ->assertSee('You have been accepted for a position.')
                ->assertSee($request->course->title)
                ->assertSee($request->staff->fullName)
                ->assertSee($request->type)
                ->assertSee('DECLINE')
                ->clickLink('Decline')
                ->waitUntilMissing('DECLINE');
            $this->assertDatabaseHas('demonstrator_applications', [
                'student_id' => $student->id,
                'request_id' => $request->id,
                'is_accepted' => true,
                'student_responded' => true,
                'student_confirms' => false,
            ]);
        });
    }
}
