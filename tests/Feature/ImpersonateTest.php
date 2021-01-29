<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ImpersonateTest extends TestCase
{
    /** @test */
    public function admin_can_impersonate_as_other_user()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $student = User::factory()->student()->create();

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
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $student = User::factory()->student()->create();

        Auth::login($admin);

        $response = $this->get(route('admin.impersonate', $staff->id));
        $this->assertEquals(Auth::id(), $staff->id);

        $response = $this->delete(route('admin.impersonate.stop'));
        $this->assertEquals(Auth::id(), $admin->id);
        $response->assertSessionMissing('original_id');
    }
}
