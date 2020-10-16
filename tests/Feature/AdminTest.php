<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\Course;
use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Models\EmailLog;
use App\Notifications\AdminManualWithdraw;
use App\Notifications\StudentContractReady;
use App\Notifications\StudentRequestWithdrawn;
use App\Notifications\StudentRTWReceived;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminTest extends TestCase
{
    /** @test */
    public function admin_can_see_list_of_students_and_their_contract_status()
    {
        $admin = User::factory()->admin()->create();
        $student1 = User::factory()->student()->create();
        $student2 = User::factory()->student()->create(['has_contract' => true]);
        $application = DemonstratorApplication::factory()->create(['student_id' => $student1->id]);
        $application = DemonstratorApplication::factory()->create(['student_id' => $student2->id]);

        $response = $this->actingAs($admin)->get(route('admin.edit_contracts'));

        $response->assertStatus(200);
        $response->assertSee($student1->fullName);
        $response->assertSee($student2->fullName);
        //TODO: check contract status somehow.   $response->assertSee()
    }

    /** @test */
    public function admin_can_update_students_contract_status()
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create(['has_contract' => false]);

        $response = $this->actingAs($admin)->postJson(route('admin.update_contracts'), ['student_id' => $student->id]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($student->fresh()->has_contract);
        Notification::assertSentTo($student, StudentContractReady::class);
    }

    /** @test */
    public function admin_can_add_students_contract_dates()
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create(['has_contract' => true]);

        $response = $this->actingAs($admin)->postJson(route('admin.contract.update_dates'), [
            'student_id' => $student->id,
            'contract_start' => Carbon::now()->format('Y-m-d'),
            'contract_end' => Carbon::now()->addYear()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertEquals(Carbon::now()->format('Y-m-d'), $student->fresh()->contract_start->format('Y-m-d'));
        $this->assertEquals(Carbon::now()->addYear()->format('Y-m-d'), $student->fresh()->contract_end->format('Y-m-d'));
    }

    /** @test */
    public function admin_can_update_students_rtw_status()
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create(['returned_rtw' => false]);

        $response = $this->actingAs($admin)->postJson(route('admin.rtw.update'), ['student_id' => $student->id]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($student->fresh()->returned_rtw);
        Notification::assertSentTo($student, StudentRTWReceived::class);
    }

    /** @test */
    public function admin_can_add_students_rtw_dates()
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create(['returned_rtw' => true]);

        $response = $this->actingAs($admin)->postJson(route('admin.rtw.update_dates'), [
            'student_id' => $student->id,
            'rtw_start' => Carbon::now()->format('Y-m-d'),
            'rtw_end' => Carbon::now()->addYear()->format('Y-m-d'),
        ]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertEquals(Carbon::now()->format('Y-m-d'), $student->fresh()->rtw_start->format('Y-m-d'));
        $this->assertEquals(Carbon::now()->addYear()->format('Y-m-d'), $student->fresh()->rtw_end->format('Y-m-d'));
    }

    /** @test */
    public function admin_can_manually_withdraw_student_from_requests()
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create(['returned_rtw' => false]);
        $application = DemonstratorApplication::factory()->create(['student_id' => $student->id]);
        $application2 = DemonstratorApplication::factory()->create(['student_id' => $student->id]);
        $application3 = DemonstratorApplication::factory()->create(['student_id' => $student->id]);
        $emaillog = EmailLog::factory()->create(['user_id' => $student->id, 'application_id' => $application->id]);

        $response = $this->actingAs($admin)->postJson(route('admin.manual_withdraw'), [
            'student_id' => $student->id,
            'applications' => [
                $application->id, $application2->id,
            ],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.edit_contracts'));
        $response->assertSessionHas(['success_message' => "$student->fullName's applications were removed."]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application2->id]);
        $this->assertDatabaseMissing('email_logs', ['id' => $emaillog->id]);
        $this->assertDatabaseHas('demonstrator_applications', ['id' => $application3->id]);

        Notification::assertSentTo($student, AdminManualWithdraw::class);
    }

    /** @test */
    public function admin_can_delete_a_student()
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create(['returned_rtw' => true, 'has_contract' => true]);
        $application = DemonstratorApplication::factory()->create(['student_id' => $student->id]);
        $application2 = DemonstratorApplication::factory()->create(['student_id' => $student->id]);
        $application3 = DemonstratorApplication::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($admin)->post(route('admin.students.destroy'), ['student_id' => $student->id]);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.edit_contracts'));
        $response->assertSessionHas(['success_message' => "All of $student->fullName's applications were removed and they were removed from the system."]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application2->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application3->id]);
        $this->assertDatabaseMissing('users', ['id' => $student->id]);
    }

    /** @test */
    public function admin_can_view_all_staff_and_requests()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $staff2 = User::factory()->staff()->create();
        $courses = Course::factory()->count(3)->create();
        $courses2 = Course::factory()->count(3)->create();
        $staff->courses()->attach($courses);
        $staff2->courses()->attach($courses2);
        DemonstratorRequest::factory()->create(['staff_id' => $staff, 'course_id' => $courses[0]]);
        DemonstratorRequest::factory()->create(['staff_id' => $staff, 'course_id' => $courses[1]]);
        DemonstratorRequest::factory()->create(['staff_id' => $staff, 'course_id' => $courses[2]]);
        DemonstratorRequest::factory()->create(['staff_id' => $staff2, 'course_id' => $courses2[0]]);
        DemonstratorRequest::factory()->create(['staff_id' => $staff2, 'course_id' => $courses2[1]]);
        DemonstratorRequest::factory()->create(['staff_id' => $staff2, 'course_id' => $courses2[2]]);

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
    public function admin_can_view_the_students_page_without_any_students_on_it()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.edit_contracts'));
        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_the_import_page()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('import.index'));
        $response->assertStatus(200);
        $response->assertSee('Import Requests');
    }

    /** @test */
    public function admin_can_view_list_of_staff()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $staff2 = User::factory()->staff()->create();
        $courses = Course::factory()->count(6)->create();
        $courses2 = Course::factory()->count(7)->create();
        $staff->courses()->attach($courses);
        $staff2->courses()->attach($courses2);
        DemonstratorRequest::factory()->create(['staff_id' => $staff, 'course_id' => $courses[0]]);
        DemonstratorRequest::factory()->create(['staff_id' => $staff, 'course_id' => $courses[1]]);
        DemonstratorRequest::factory()->create(['staff_id' => $staff2, 'course_id' => $courses2[0]]);
        DemonstratorRequest::factory()->create(['staff_id' => $staff2, 'course_id' => $courses2[1]]);
        DemonstratorRequest::factory()->create(['staff_id' => $staff2, 'course_id' => $courses2[2]]);

        $response = $this->actingAs($admin)->get(route('admin.staff.index'));

        $response->assertStatus(200);
        $response->assertSee($staff->fullName);
        $response->assertSee($staff2->fullName);
        $response->assertSee($staff->username);
        $response->assertSee($staff2->username);
        $response->assertSee($staff->email);
        $response->assertSee($staff2->email);
    }

    /** @test */
    public function can_add_academic_to_a_course()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $course1 = Course::factory()->create();
        $this->assertCount(0, $staff->courses);

        $response = $this->actingAs($admin)->postJson(
            route('admin.staff.update'),
            [
                'staff_id' => $staff->id,
                'course_id' => $course1->id,
            ]
        );
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertCount(1, $staff->fresh()->courses);
    }

    /** @test */
    public function can_remove_academic_from_a_course()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $course1 = Course::factory()->create();
        $course2 = Course::factory()->create();
        $staff->courses()->attach([$course1->id, $course2->id]);
        $this->assertCount(2, $staff->courses);

        $response = $this->actingAs($admin)->postJson(
            route('admin.staff.removeCourse'),
            [
                'staff_id' => $staff->id,
                'course_id' => $course1->id,
            ]
        );
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertCount(1, $staff->fresh()->courses);
    }

    /** @test */
    public function can_get_info_about_a_staff_member_relationship_without_requests_and_applications()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $course->staff()->attach($staff);

        $response = $this->actingAs($admin)->get(
            route('admin.staff.courseInfo', [
                'staff_id' => $staff->id,
                'course_id' => $course->id,
            ])
        );
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK',
            'requests' => false,
            'applications' => false,
        ]);
    }

    /** @test */
    public function can_get_info_about_a_staff_member_relationship_with_requests_without_applications()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $course->staff()->attach($staff);
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id, 'course_id' => $course->id]);

        $response = $this->actingAs($admin)->get(
            route('admin.staff.courseInfo', [
                'staff_id' => $staff->id,
                'course_id' => $course->id,
            ])
        );
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK',
            'requests' => true,
            'applications' => false,
        ]);
    }

    /** @test */
    public function can_get_info_about_a_staff_member_relationship_with_requests_and_applications()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $course->staff()->attach($staff);
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
        $application = DemonstratorApplication::factory()->create(['request_id' => $request->id]);

        $response = $this->actingAs($admin)->get(
            route('admin.staff.courseInfo', [
                'staff_id' => $staff->id,
                'course_id' => $course->id,
            ])
        );
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK',
            'requests' => true,
            'applications' => true,
        ]);
    }

    /** @test */
    public function can_remove_all_demonstrator_requests_for_a_given_staff_member_for_a_given_course()
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $course->staff()->attach($staff);
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
        $application = DemonstratorApplication::factory()->create(['request_id' => $request->id]);
        $request2 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);
        $request3 = DemonstratorRequest::factory()->create(['course_id' => $course->id]);
        $application2 = DemonstratorApplication::factory()->create(['request_id' => $request3->id]);

        $response = $this->actingAs($admin)->post(
            route('admin.staff.removeRequests'),
            [
                'staff_id' => $staff->id,
                'course_id' => $course->id,
            ]
        );
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK',
        ]);
        $this->assertFalse($staff->courses->contains($course));
        $this->assertDatabaseMissing('demonstrator_requests', ['id' => $request->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $application->id]);
        $this->assertDatabaseHas('demonstrator_requests', ['id' => $request2->id]);
        $this->assertDatabaseHas('demonstrator_requests', ['id' => $request3->id]);
        $this->assertDatabaseHas('demonstrator_applications', ['id' => $application2->id]);
        Notification::assertSentTo($application->student, StudentRequestWithdrawn::class);
    }

    /** @test */
    public function can_reassign_demonstrator_requests_for_a_given_staff_member_for_a_given_course_to_another_staff_member()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $staff2 = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $course->staff()->attach($staff);
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
        $request2 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($admin)->post(
            route('admin.staff.reassignRequests'),
            [
                'staff_id' => $staff->id,
                'course_id' => $course->id,
                'reassign_id' => $staff2->id,
            ]
        );
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK',
        ]);
        $this->assertTrue($staff2->courses->contains($course));
        $this->assertFalse($staff->courses->contains($course));
        $this->assertEquals($request->fresh()->staff_id, $staff2->id);
        $this->assertEquals($request2->fresh()->staff_id, $staff->id);
    }

    /** @test */
    public function cant_reassign_demonstrator_requests_for_a_given_staff_member_for_a_given_course_to_another_staff_member_on_the_same_course()
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->staff()->create();
        $staff2 = User::factory()->staff()->create();
        $course = Course::factory()->create();
        $course->staff()->attach([$staff->id, $staff2->id]);
        $request = DemonstratorRequest::factory()->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
        $request2 = DemonstratorRequest::factory()->create(['staff_id' => $staff->id]);

        $response = $this->actingAs($admin)->post(
            route('admin.staff.reassignRequests'),
            [
                'staff_id' => $staff->id,
                'course_id' => $course->id,
                'reassign_id' => $staff2->id,
            ]
        );
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'Cannot allocate to person on the same course.',
        ]);
        $this->assertTrue($staff2->courses->contains($course));
        $this->assertTrue($staff->courses->contains($course));
        $this->assertEquals($request->fresh()->staff_id, $staff->id);
        $this->assertEquals($request2->fresh()->staff_id, $staff->id);
    }

    /** @test */
    public function admin_can_remove_all_student_data()
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create([
            'has_contract' => true,
            'contract_end' => Carbon::now()->subDays(1),
        ]);
        $student2 = User::factory()->student()->create([
            'has_contract' => true,
            'contract_end' => Carbon::now()->addDays(1),
        ]);
        $application = DemonstratorApplication::factory()->create(['student_id' => $student->id]);
        $application2 = DemonstratorApplication::factory()->create(['student_id' => $student2->id]);
        $emailLog = EmailLog::factory()->create(['user_id' => $student->id]);
        $emailLog2 = EmailLog::factory()->create(['user_id' => $student2->id]);

        $response = $this->actingAs($admin)->post(route('admin.students.hoover'));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.edit_contracts'));

        $this->assertDatabaseMissing('demonstrator_applications', ['student_id' => $student->id]);
        $this->assertDatabaseMissing('email_logs', ['user_id' => $student->id]);
        $this->assertDatabaseMissing('users', ['id' => $student->id]);

        $this->assertDatabaseMissing('demonstrator_applications', ['student_id' => $student2->id]);
        $this->assertDatabaseMissing('email_logs', ['user_id' => $student2->id]);
        $this->assertDatabaseMissing('users', ['id' => $student2->id]);
    }

    /** @test */
    public function admin_can_create_a_new_ldap_student()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.students.store'), [
            'username' => 'test123',
            'email' => 'tesat@example.com',
            'forenames' => 'ABC',
            'surname' => 'ASFAFWF',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.edit_contracts'));
        $response->assertSessionHas(['success']);
        $this->assertDatabaseHas('users', [
            'username' => 'test123',
            'email' => 'tesat@example.com',
            'forenames' => 'ABC',
            'surname' => 'ASFAFWF',
            'is_admin' => false,
            'is_student' => true,
            'returned_rtw' => false,
            'rtw_notified' => false,
            'has_contract' => false,
            'notes' => null,
            'hide_blurb' => false,
        ]);
    }

    /** @test */
    public function admin_can_create_a_new_ldap_staff()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.staff.store'), [
            'username' => 'test123',
            'email' => 'tesat@example.com',
            'forenames' => 'ABC',
            'surname' => 'ASFAFWF',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas(['success']);
        $this->assertDatabaseHas('users', [
            'username' => 'test123',
            'email' => 'tesat@example.com',
            'forenames' => 'ABC',
            'surname' => 'ASFAFWF',
            'is_admin' => false,
            'is_student' => false,
            'returned_rtw' => false,
            'rtw_notified' => false,
            'has_contract' => false,
            'notes' => null,
            'hide_blurb' => false,
        ]);
    }
}
