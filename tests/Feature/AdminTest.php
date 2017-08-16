<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\DemonstratorApplication;
use App\Notifications\AdminManualWithdraw;
use App\Notifications\StudentContractReady;
use App\Notifications\StudentRTWReceived;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function admin_can_see_list_of_students_and_their_contract_status () {
        $admin = factory(User::class)->states('admin')->create();
        $student1 = factory(User::class)->states('student')->create();
        $student2 = factory(User::class)->states('student')->create(['has_contract' => true]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student1->id]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student2->id]);

        $response = $this->actingAs($admin)->get(route('admin.edit_contracts'));

        $response->assertStatus(200);
        $response->assertSee($student1->fullName);
        $response->assertSee($student2->fullName);
        //TODO: check contract status somehow.   $response->assertSee()
    }

    /** @test */
    public function admin_can_update_students_contract_status () {
        Notification::fake();
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create(['has_contract' => false]);

        $response = $this->actingAs($admin)->postJson(route('admin.update_contracts'), ['student_id' => $student->id]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($student->fresh()->has_contract);
        Notification::assertSentTo($student, StudentContractReady::class);
    }

    /** @test */
    public function admin_can_update_students_rtw_status () {
        Notification::fake();
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create(['returned_rtw' => false]);

        $response = $this->actingAs($admin)->postJson(route('admin.update_rtw'), ['student_id' => $student->id]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($student->fresh()->returned_rtw);
        Notification::assertSentTo($student, StudentRTWReceived::class);
    }

    /** @test */
    public function admin_can_manually_withdraw_student_from_requests () {
        Notification::fake();
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create(['returned_rtw' => false]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $application3 = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);

        $response = $this->actingAs($admin)->postJson(route('admin.manual_withdraw'), [
            'student_id' => $student->id,
            'applications' => [
                $application->id, $application2->id
            ]
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.edit_contracts'));
        $response->assertSessionHas(['success_message' => "{{ $student->fullName }}'s requests were removed"]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application2->id]);
        $this->assertDatabaseHas('demonstrator_applications', ['id' => $application3->id]);
        Notification::assertSentTo($student, AdminManualWithdraw::class);
    }
}
