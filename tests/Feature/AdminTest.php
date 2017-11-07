<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;


use App\User;
use App\Course;
use Tests\TestCase;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use App\Notifications\StudentRTWReceived;
use App\Notifications\AdminManualWithdraw;
use App\Notifications\StudentContractReady;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
        $response->assertSessionHas(['success_message' => "$student->fullName's applications were removed."]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application2->id]);
        $this->assertDatabaseHas('demonstrator_applications', ['id' => $application3->id]);
        Notification::assertSentTo($student, AdminManualWithdraw::class);
    }

    /** @test */
    public function admin_can_mega_delete () {
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create(['returned_rtw' => true, 'has_contract' => true]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $application3 = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);

        $response = $this->actingAs($admin)->post(route('admin.mega_delete'), ['student_id' => $student->id]);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.edit_contracts'));
        $response->assertSessionHas(['success_message' => "All of $student->fullName's applications were removed and reset their RTW and contract status."]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application2->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application3->id]);
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'has_contract' => false,
            'returned_rtw' => false
        ]);
    }

    /** @test */
    public function admin_can_view_all_staff_and_requests()
    {
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $staff2 = factory(User::class)->states('staff')->create();
        $courses = factory(Course::class, 3)->create();
        $courses2 = factory(Course::class, 3)->create();
        $staff->courses()->attach($courses);
        $staff2->courses()->attach($courses2);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff, 'course_id' => $courses[0]]);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff, 'course_id' => $courses[1]]);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff, 'course_id' => $courses[2]]);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff2, 'course_id' => $courses2[0]]);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff2, 'course_id' => $courses2[1]]);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff2, 'course_id' => $courses2[2]]);

        $response = $this->actingAs($admin)->get(route('admin.requests'));

        $response->assertStatus(200);
        $response->assertSee($courses[0]->title);
        $response->assertSee($courses[1]->title);
        $response->assertSee($courses[2]->title);
        $response->assertSee($courses2[0]->title);
        $response->assertSee($courses2[1]->title);
        $response->assertSee($courses2[2]->title);
    }

    /** @test */
    public function admin_can_view_the_students_page_without_any_students_on_it () {
        $admin = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($admin)->get(route('admin.edit_contracts'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_the_import_page () {
        $admin = factory(User::class)->states('admin')->create();

        $response = $this->actingAs($admin)->get(route('import.index'));
        $response->assertStatus(200);
        $response->assertSee('Import Requests');
    }

    /** @test */
    public function admin_can_view_list_of_staff ()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $staff2 = factory(User::class)->states('staff')->create();
        $courses = factory(Course::class, 6)->create();
        $courses2 = factory(Course::class, 7)->create();
        $staff->courses()->attach($courses);
        $staff2->courses()->attach($courses2);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff, 'course_id' => $courses[0]]);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff, 'course_id' => $courses[1]]);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff2, 'course_id' => $courses2[0]]);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff2, 'course_id' => $courses2[1]]);
        factory(DemonstratorRequest::class)->create(['staff_id' => $staff2, 'course_id' => $courses2[2]]);

        $response = $this->actingAs($admin)->get(route('admin.staff'));

        $response->assertStatus(200);
        $response->assertSee("$staff->surname, $staff->forenames");
        $response->assertSee("$staff2->surname, $staff2->forenames");
        $response->assertSee($staff->username);
        $response->assertSee($staff2->username);
        $response->assertSee($staff->email);
        $response->assertSee($staff2->email);
        $response->assertSee('6 courses'); //staff courses
        $response->assertSee('7 courses'); //staff2 courses
        $response->assertSee('2 requests'); //staff requests
        $response->assertSee('3 requests'); //staff2 requests
    }
}
