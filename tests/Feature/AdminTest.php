<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\User;
use App\Course;
use App\EmailLog;
use Carbon\Carbon;
use Tests\TestCase;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use App\Notifications\StudentRTWReceived;
use App\Notifications\AdminManualWithdraw;
use App\Notifications\StudentContractReady;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StudentRequestWithdrawn;

class AdminTest extends TestCase
{
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
    public function admin_can_add_students_contract_dates()
    {
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create(['has_contract' => true]);

        $response = $this->actingAs($admin)->postJson(route('admin.contract.update_dates'), [
            'student_id' => $student->id,
            'contract_start' => Carbon::now()->format('Y-m-d'),
            'contract_end' => Carbon::now()->addYear()->format('Y-m-d')
        ]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertEquals(Carbon::now()->format('Y-m-d'), $student->fresh()->contract_start->format('Y-m-d'));
        $this->assertEquals(Carbon::now()->addYear()->format('Y-m-d'), $student->fresh()->contract_end->format('Y-m-d'));
    }

    /** @test */
    public function admin_can_update_students_rtw_status () {
        Notification::fake();
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create(['returned_rtw' => false]);

        $response = $this->actingAs($admin)->postJson(route('admin.rtw.update'), ['student_id' => $student->id]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertTrue($student->fresh()->returned_rtw);
        Notification::assertSentTo($student, StudentRTWReceived::class);
    }

    /** @test */
    public function admin_can_add_students_rtw_dates()
    {
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create(['returned_rtw' => true]);

        $response = $this->actingAs($admin)->postJson(route('admin.rtw.update_dates'), [
            'student_id' => $student->id,
            'rtw_start' => Carbon::now()->format('Y-m-d'),
            'rtw_end' => Carbon::now()->addYear()->format('Y-m-d')
        ]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertEquals(Carbon::now()->format('Y-m-d'), $student->fresh()->rtw_start->format('Y-m-d'));
        $this->assertEquals(Carbon::now()->addYear()->format('Y-m-d'), $student->fresh()->rtw_end->format('Y-m-d'));
    }

    /** @test */
    public function admin_can_manually_withdraw_student_from_requests () {
        Notification::fake();
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create(['returned_rtw' => false]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $application3 = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $emaillog = factory(EmailLog::class)->create(['user_id' => $student->id, 'application_id' => $application->id]);

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
        $this->assertDatabaseMissing('email_logs', ['id' => $emaillog->id]);
        $this->assertDatabaseHas('demonstrator_applications', ['id' => $application3->id]);

        Notification::assertSentTo($student, AdminManualWithdraw::class);
    }

    /** @test */
    public function admin_can_delete_a_student () {
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create(['returned_rtw' => true, 'has_contract' => true]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $application3 = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);

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
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $course1 = factory(Course::class)->create();
        $this->assertCount(0, $staff->courses);

        $response = $this->actingAs($admin)->postJson(
            route('admin.staff.update'), [
                'staff_id' => $staff->id,
                'course_id' => $course1->id
            ]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertCount(1, $staff->fresh()->courses);
    }

    /** @test */
    public function can_remove_academic_from_a_course()
    {
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $course1 = factory(Course::class)->create();
        $course2 = factory(Course::class)->create();
        $staff->courses()->attach([$course1->id, $course2->id]);
        $this->assertCount(2, $staff->courses);

        $response = $this->actingAs($admin)->postJson(
            route('admin.staff.removeCourse'),
            [
                'staff_id' => $staff->id,
                'course_id' => $course1->id
            ]
        );
        $response->assertStatus(200);
        $response->assertJson(['status' => 'OK']);
        $this->assertCount(1, $staff->fresh()->courses);
    }

    /** @test */
    public function can_get_info_about_a_staff_member_relationship_without_requests_and_applications()
    {
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $course = factory(Course::class)->create();
        $course->staff()->attach($staff);

        $response = $this->actingAs($admin)->get(
            route('admin.staff.courseInfo', [
                'staff_id' => $staff->id,
                'course_id' => $course->id
            ])
        );
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK',
            'requests' => false,
            'applications' => false
        ]);
    }

    /** @test */
    public function can_get_info_about_a_staff_member_relationship_with_requests_without_applications()
    {
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $course = factory(Course::class)->create();
        $course->staff()->attach($staff);
        $request = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'course_id' => $course->id]);

        $response = $this->actingAs($admin)->get(
            route('admin.staff.courseInfo', [
                'staff_id' => $staff->id,
                'course_id' => $course->id
            ])
        );
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK',
            'requests' => true,
            'applications' => false
        ]);
    }

    /** @test */
    public function can_get_info_about_a_staff_member_relationship_with_requests_and_applications()
    {
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $course = factory(Course::class)->create();
        $course->staff()->attach($staff);
        $request = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
        $application = factory(DemonstratorApplication::class)->create(['request_id' => $request->id]);

        $response = $this->actingAs($admin)->get(
            route('admin.staff.courseInfo', [
                'staff_id' => $staff->id,
                'course_id' => $course->id
            ])
        );
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'OK',
            'requests' => true,
            'applications' => true
        ]);
    }

    /** @test */
    public function can_remove_all_demonstrator_requests_for_a_given_staff_member_for_a_given_course ()
    {
        Notification::fake();
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $course = factory(Course::class)->create();
        $course->staff()->attach($staff);
        $request = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
        $application = factory(DemonstratorApplication::class)->create(['request_id' => $request->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id]);
        $request3 = factory(DemonstratorRequest::class)->create(['course_id' => $course->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['request_id' => $request3->id]);

        $response = $this->actingAs($admin)->post(
            route('admin.staff.removeRequests'), [
                'staff_id' => $staff->id,
                'course_id' => $course->id
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
    public function can_reassign_demonstrator_requests_for_a_given_staff_member_for_a_given_course_to_another_staff_member ()
    {
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $staff2 = factory(User::class)->states('staff')->create();
        $course = factory(Course::class)->create();
        $course->staff()->attach($staff);
        $request = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id]);

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
    public function cant_reassign_demonstrator_requests_for_a_given_staff_member_for_a_given_course_to_another_staff_member_on_the_same_course ()
    {
        $admin = factory(User::class)->states('admin')->create();
        $staff = factory(User::class)->states('staff')->create();
        $staff2 = factory(User::class)->states('staff')->create();
        $course = factory(Course::class)->create();
        $course->staff()->attach([$staff->id, $staff2->id]);
        $request = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id, 'course_id' => $course->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['staff_id' => $staff->id]);

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
    public function admin_can_remove_all_student_data ()
    {
        $admin = factory(User::class)->states('admin')->create();
        $student = factory(User::class)->states('student')->create([
            'has_contract' => true,
            'contract_end' => Carbon::now()->subDays(1),
        ]);
        $student2 = factory(User::class)->states('student')->create([
            'has_contract' => true,
            'contract_end' => Carbon::now()->addDays(1),
        ]);
        $application = factory(DemonstratorApplication::class)->create(['student_id' => $student->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['student_id' => $student2->id]);
        $emailLog = factory(EmailLog::class)->create(['user_id' => $student->id]);
        $emailLog2 = factory(EmailLog::class)->create(['user_id' => $student2->id]);

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
        $admin = factory(User::class)->states('admin')->create();

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
        $admin = factory(User::class)->states('admin')->create();

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
