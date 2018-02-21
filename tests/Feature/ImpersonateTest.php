<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ImpersonateTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function admin_can_impersonate_as_other_user()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $student = factory(User::class)->states('student')->create();

        Auth::login($admin);

        $response = $this->get(route('admin.impersonate', $staff->id));

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->assertEquals(Auth::id(), $staff->id);

        Auth::login($admin);

        $response = $this->get(route('admin.impersonate', $student->id));

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->assertEquals(Auth::id(), $student->id);

        $response->assertSessionHas(['original_id' => $admin->id]);
    }

    /** @test */
    public function admin_can_stop_impersonating_as_other_user()
    {
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $student = factory(User::class)->states('student')->create();

        Auth::login($admin);

        $response = $this->get(route('admin.impersonate', $staff->id));
        $this->assertEquals(Auth::id(), $staff->id);

        $response = $this->delete(route('admin.impersonate.stop'));
        $this->assertEquals(Auth::id(), $admin->id);
        $response->assertSessionMissing('original_id');
    }
}
