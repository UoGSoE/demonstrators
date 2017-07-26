<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function admin_can_see_list_of_students_and_their_contract_status () {
        $admin = factory(User::class)->states('admin')->create();
        $student1 = factory(User::class)->states('student')->create();
        $student2 = factory(User::class)->states('student')->create(['has_contract' => true]);

        $response = $this->actingAs($admin)->get(route('admin.edit_contracts'));

        $response->assertStatus(200);
        $response->assertSee($student1->fullName);
        $response->assertSee($student2->fullName);
        //TODO: check contract status somehow.   $response->assertSee()
    }

    /** @test */
    public function admin_can_update_students_contract_status () {
        $admin = factory(User::class)->states('admin')->create();
        $student1 = factory(User::class)->states('student')->create(['has_contract' => false]);

        $response = $this->actingAs($admin)->postJson(route('admin.update_contracts'), ['student_id' => $student1->id]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($student1->fresh()->has_contract);
    }
}
