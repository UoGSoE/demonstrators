<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    /** @test */
    public function admins_can_toggle_admin_permissions_for_other_users ()
    {
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $this->assertFalse($staff->is_admin);

        $response = $this->actingAs($admin)->postJson(route('admin.permissions', $staff->id));

        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($staff->fresh()->is_admin);
    }
}
