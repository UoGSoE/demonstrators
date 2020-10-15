<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\Course;
use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Models\EmailLog;
use App\Models\User;
use Carbon\Carbon;
use Ohffs\SimpleSpout\ExcelSheet;
use Tests\TestCase;

class ExportTest extends TestCase
{
    /** @test */
    public function can_export_output_1()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $course1 = factory(Course::class)->create(['title' => 'ABC']);
        $staff1 = factory(User::class)->states('staff')->create();
        $staff1->courses()->attach($course1->id);

        $course2 = factory(Course::class)->create(['title' => 'DEF']);
        $staff2 = factory(User::class)->states('staff')->create();
        $staff2->courses()->attach($course2->id);

        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $course1->id, 'staff_id' => $staff1->id, 'type' => 'Demonstrator']);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $course2->id, 'staff_id' => $staff2->id, 'type' => 'Tutor']);
        $request3 = factory(DemonstratorRequest::class)->create(['course_id' => $course2->id, 'staff_id' => $staff2->id, 'type' => 'Marker']);

        $response = $this->actingAs($admin)->get(route('admin.reports.output1.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=output1.xlsx');
        $file = $response->getFile();
        $data = (new ExcelSheet)->import($file->getPathname());
        $headings = array_shift($data);
        $row1 = array_shift($data);
        $row2 = array_shift($data);
        $row3 = array_shift($data);

        $this->assertEquals($request1->course->subject, $row1[0]); //Subject
        $this->assertEquals($request1->course->catalogue, $row1[1]); //Cat
        $this->assertEquals($request1->start_date, $row1[2]); //Start Date
        $this->assertEquals($request1->course->title, $row1[3]); //Long Title
        $this->assertEquals($request1->staff->fullName, $row1[4]); //Associated Academic
        $this->assertEquals($request1->staff->username, $row1[5]); //Main Ac GUID
        $this->assertEquals($request1->staff->email, $row1[6]); //Email
        $this->assertEquals($request1->staff->forenames, $row1[7]); //Academic First Name
        $this->assertEquals('', $row1[8]); //Add Ac Staff Req
        $this->assertEquals('', $row1[9]); //Ad Ac Staff Name
        $this->assertEquals($request1->getNumberUnfilled(), $row1[10]); //No . Demos Requested
        $this->assertEquals($request1->demonstrators_needed, $row1[11]); //No . Demos Unfilled
        $this->assertEquals($request1->hours_needed, $row1[12]); //Hours / Demo
        $this->assertEquals($request1->hours_training, $row1[13]); //Train / Demo
        $this->assertEquals('', $row1[14]); //No . Tutors Requested
        $this->assertEquals('', $row1[15]); //No . Tutors Unfilled
        $this->assertEquals('', $row1[16]); //Hours / Tutor
        $this->assertEquals('', $row1[17]); //Train / Tutor
        $this->assertEquals('', $row1[18]); //No . Markers Requested
        $this->assertEquals('', $row1[19]); //No . Markers Unfilled
        $this->assertEquals('', $row1[20]); //Hours / Marker
        $this->assertEquals('', $row1[21]); //Train / Marker
        $this->assertEquals('', $row1[22]); //Same person as Add Ac ?
        $this->assertEquals($request1->type, $row1[23]); //Activity Type
        $this->assertEquals('', $row1[24]); //Dual Activity
        $this->assertEquals('', $row1[25]); //Same person for dual
        $this->assertEquals('', $row1[26]); //Lab content / subject
        $this->assertEquals($request1->skills, $row1[27]); //Special Requirements
        $this->assertEquals($request1->getSemesters(), $row1[28]); //Semester
        $this->assertEquals('', $row1[29]); //Ac Response
        $this->assertEquals('', $row1[30]); //Notes
        $this->assertEquals('', $row1[31]); //Open to Y4 / 5 / PGT
        $this->assertEquals('', $row1[32]); //Offered
        $this->assertEquals('', $row1[33]); //Respondents
        $this->assertEquals('', $row1[34]); //Messages
        $this->assertEquals('', $row1[35]); //Referred
        $this->assertEquals('', $row1[36]); //Filled
        $this->assertEquals('', $row1[37]); //Contract Email
        $this->assertEquals('', $row1[38]); //RtW / EWP sent
        $this->assertEquals('', $row1[39]); //Contract Produced
        $this->assertEquals('', $row1[40]); //Contract Returned

        $this->assertEquals($request2->course->subject, $row2[0]); //Subject
        $this->assertEquals($request2->course->catalogue, $row2[1]); //Cat
        $this->assertEquals($request2->start_date, $row2[2]); //Start Date
        $this->assertEquals($request2->course->title, $row2[3]); //Long Title
        $this->assertEquals($request2->staff->fullName, $row2[4]); //Associated Academic
        $this->assertEquals($request2->staff->username, $row2[5]); //Main Ac GUID
        $this->assertEquals($request2->staff->email, $row2[6]); //Email
        $this->assertEquals($request2->staff->forenames, $row2[7]); //Academic First Name
        $this->assertEquals('', $row2[8]); //Add Ac Staff Req
        $this->assertEquals('', $row2[9]); //Ad Ac Staff Name
        $this->assertEquals('', $row2[10]); //No . Demos Requested
        $this->assertEquals('', $row2[11]); //No . Demos Unfilled
        $this->assertEquals('', $row2[12]); //Hours / Demo
        $this->assertEquals('', $row2[13]); //Train / Demo
        $this->assertEquals($request2->getNumberUnfilled(), $row2[14]); //No . Tutors Requested
        $this->assertEquals($request2->demonstrators_needed, $row2[15]); //No . Tutors Unfilled
        $this->assertEquals($request2->hours_needed, $row2[16]); //Hours / Tutor
        $this->assertEquals($request2->hours_training, $row2[17]); //Train / Tutor
        $this->assertEquals('', $row2[18]); //No . Markers Requested
        $this->assertEquals('', $row2[19]); //No . Markers Unfilled
        $this->assertEquals('', $row2[20]); //Hours / Marker
        $this->assertEquals('', $row2[21]); //Train / Marker
        $this->assertEquals('', $row2[22]); //Same person as Add Ac ?
        $this->assertEquals($request2->type, $row2[23]); //Activity Type
        $this->assertEquals('', $row2[24]); //Dual Activity
        $this->assertEquals('', $row2[25]); //Same person for dual
        $this->assertEquals('', $row2[26]); //Lab content / subject
        $this->assertEquals($request2->skills, $row2[27]); //Special Requirements
        $this->assertEquals($request2->getSemesters(), $row2[28]); //Semester
        $this->assertEquals('', $row2[29]); //Ac Response
        $this->assertEquals('', $row2[30]); //Notes
        $this->assertEquals('', $row2[31]); //Open to Y4 / 5 / PGT
        $this->assertEquals('', $row2[32]); //Offered
        $this->assertEquals('', $row2[33]); //Respondents
        $this->assertEquals('', $row2[34]); //Messages
        $this->assertEquals('', $row2[35]); //Referred
        $this->assertEquals('', $row2[36]); //Filled
        $this->assertEquals('', $row2[37]); //Contract Email
        $this->assertEquals('', $row2[38]); //RtW / EWP sent
        $this->assertEquals('', $row2[39]); //Contract Produced
        $this->assertEquals('', $row2[40]); //Contract Returned

        $this->assertEquals($request3->course->subject, $row3[0]); //Subject
        $this->assertEquals($request3->course->catalogue, $row3[1]); //Cat
        $this->assertEquals($request3->start_date, $row3[2]); //Start Date
        $this->assertEquals($request3->course->title, $row3[3]); //Long Title
        $this->assertEquals($request3->staff->fullName, $row3[4]); //Associated Academic
        $this->assertEquals($request3->staff->username, $row3[5]); //Main Ac GUID
        $this->assertEquals($request3->staff->email, $row3[6]); //Email
        $this->assertEquals($request3->staff->forenames, $row3[7]); //Academic First Name
        $this->assertEquals('', $row3[8]); //Add Ac Staff Req
        $this->assertEquals('', $row3[9]); //Ad Ac Staff Name
        $this->assertEquals('', $row3[10]); //No . Demos Requested
        $this->assertEquals('', $row3[11]); //No . Demos Unfilled
        $this->assertEquals('', $row3[12]); //Hours / Demo
        $this->assertEquals('', $row3[13]); //Train / Demo
        $this->assertEquals('', $row3[14]); //No . Tutors Requested
        $this->assertEquals('', $row3[15]); //No . Tutors Unfilled
        $this->assertEquals('', $row3[16]); //Hours / Tutor
        $this->assertEquals('', $row3[17]); //Train / Tutor
        $this->assertEquals($request3->getNumberUnfilled(), $row3[18]); //No . Markers Requested
        $this->assertEquals($request3->demonstrators_needed, $row3[19]); //No . Markers Unfilled
        $this->assertEquals($request3->hours_needed, $row3[20]); //Hours / Marker
        $this->assertEquals($request3->hours_training, $row3[21]); //Train / Marker
        $this->assertEquals('', $row3[22]); //Same person as Add Ac ?
        $this->assertEquals($request3->type, $row3[23]); //Activity Type
        $this->assertEquals('', $row3[24]); //Dual Activity
        $this->assertEquals('', $row3[25]); //Same person for dual
        $this->assertEquals('', $row3[26]); //Lab content / subject
        $this->assertEquals($request3->skills, $row3[27]); //Special Requirements
        $this->assertEquals($request3->getSemesters(), $row3[28]); //Semester
        $this->assertEquals('', $row3[29]); //Ac Response
        $this->assertEquals('', $row3[30]); //Notes
        $this->assertEquals('', $row3[31]); //Open to Y4 / 5 / PGT
        $this->assertEquals('', $row3[32]); //Offered
        $this->assertEquals('', $row3[33]); //Respondents
        $this->assertEquals('', $row3[34]); //Messages
        $this->assertEquals('', $row3[35]); //Referred
        $this->assertEquals('', $row3[36]); //Filled
        $this->assertEquals('', $row3[37]); //Contract Email
        $this->assertEquals('', $row3[38]); //RtW / EWP sent
        $this->assertEquals('', $row3[39]); //Contract Produced
        $this->assertEquals('', $row3[40]); //Contract Returned
    }

    /** @test */
    public function can_export_output_2()
    {
        $admin = factory(User::class)->states('admin')->create();
        $course1 = factory(Course::class)->create(['title' => 'ABC']);
        $staff1 = factory(User::class)->states('staff')->create();
        $staff1->courses()->attach($course1->id);

        $course2 = factory(Course::class)->create(['title' => 'DEF']);
        $staff2 = factory(User::class)->states('staff')->create();
        $staff2->courses()->attach($course2->id);

        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $course1->id, 'staff_id' => $staff1->id, 'type' => 'Demonstrator']);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $course2->id, 'staff_id' => $staff2->id, 'type' => 'Marker']);

        $student1 = factory(User::class)->create();
        $student2 = factory(User::class)->create();
        $student3 = factory(User::class)->create();
        $application1 = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id, 'student_id' => $student1->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id, 'student_id' => $student2->id]);
        $application3 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id, 'student_id' => $student3->id]);
        $emaillog1 = factory(EmailLog::class)->create(['user_id' => $student1->id, 'notification' => 'App\Notifications\StudentRTWInfo']);
        $emaillog2 = factory(EmailLog::class)->create(['user_id' => $student1->id, 'notification' => 'App\Notifications\StudentRTWReceived']);
        $emaillog3 = factory(EmailLog::class)->create(['user_id' => $student1->id, 'notification' => 'App\Notifications\StudentContractReady']);
        $emaillog4 = factory(EmailLog::class)->create(['user_id' => $student1->id, 'application_id' => $application1->id, 'notification' => 'App\Notifications\AcademicAcceptsStudent']);

        $response = $this->actingAs($admin)->get(route('admin.reports.output2.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=output2.xlsx');
        $file = $response->getFile();
        $data = (new ExcelSheet)->import($file->getPathname());
        $headings = array_shift($data);
        $row1 = array_shift($data);
        $row2 = array_shift($data);
        $row3 = array_shift($data);
        $this->assertEquals($application1->request->course->code, $row1[0]);
        $this->assertEquals($application1->request->course->title, $row1[1]);
        $this->assertEquals($application1->request->staff->fullName, $row1[2]);
        $this->assertEquals($application1->request->staff->email, $row1[3]);
        $this->assertEquals($application1->student->fullName, $row1[4]);
        $this->assertEquals($application1->student->email, $row1[5]);
        $this->assertEquals($application1->student->getDateOf('StudentRTWInfo'), $row1[6]);
        $this->assertEquals($application1->student->getDateOf('StudentRTWReceived'), $row1[7]);
        $this->assertEquals($application1->student->getDateOf('StudentContractReady'), $row1[8]);
        $this->assertEquals($application1->student->getDateOf('AcademicAcceptsStudent', $application1->request, 'Demonstrator'), $row1[9]);
        $this->assertEquals($application1->student->getDateOf('StudentConfirm', $application1->request, 'Demonstrator'), $row1[10]);
        $this->assertEquals($application1->student->getDateOf('AcademicAcceptsStudent', $application1->request, 'Marker'), $row1[11]);
        $this->assertEquals($application1->student->getDateOf('StudentConfirm', $application1->request, 'Marker'), $row1[12]);
        $this->assertEquals($application1->student->getDateOf('AcademicAcceptsStudent', $application1->request, 'Tutor'), $row1[13]);
        $this->assertEquals($application1->student->getDateOf('StudentConfirm', $application1->request, 'Tutor'), $row1[14]);

        $this->assertEquals($application2->request->course->code, $row2[0]);
        $this->assertEquals($application2->request->course->title, $row2[1]);
        $this->assertEquals($application2->request->staff->fullName, $row2[2]);
        $this->assertEquals($application2->request->staff->email, $row2[3]);
        $this->assertEquals($application2->student->fullName, $row2[4]);
        $this->assertEquals($application2->student->email, $row2[5]);
        $this->assertEquals($application2->student->getDateOf('StudentRTWInfo'), $row2[6]);
        $this->assertEquals($application2->student->getDateOf('StudentRTWReceived'), $row2[7]);
        $this->assertEquals($application2->student->getDateOf('StudentContractReady'), $row2[8]);
        $this->assertEquals($application2->student->getDateOf('AcademicAcceptsStudent', $application2->request, 'Demonstrator'), $row2[9]);
        $this->assertEquals($application2->student->getDateOf('StudentConfirm', $application2->request, 'Demonstrator'), $row2[10]);
        $this->assertEquals($application2->student->getDateOf('AcademicAcceptsStudent', $application2->request, 'Marker'), $row2[11]);
        $this->assertEquals($application2->student->getDateOf('StudentConfirm', $application2->request, 'Marker'), $row2[12]);
        $this->assertEquals($application2->student->getDateOf('AcademicAcceptsStudent', $application2->request, 'Tutor'), $row2[13]);
        $this->assertEquals($application2->student->getDateOf('StudentConfirm', $application2->request, 'Tutor'), $row2[14]);

        $this->assertEquals($application3->request->course->code, $row3[0]);
        $this->assertEquals($application3->request->course->title, $row3[1]);
        $this->assertEquals($application3->request->staff->fullName, $row3[2]);
        $this->assertEquals($application3->request->staff->email, $row3[3]);
        $this->assertEquals($application3->student->fullName, $row3[4]);
        $this->assertEquals($application3->student->email, $row3[5]);
        $this->assertEquals($application3->student->getDateOf('StudentRTWInfo'), $row3[6]);
        $this->assertEquals($application3->student->getDateOf('StudentRTWReceived'), $row3[7]);
        $this->assertEquals($application3->student->getDateOf('StudentContractReady'), $row3[8]);
        $this->assertEquals($application3->student->getDateOf('AcademicAcceptsStudent', $application3->request, 'Demonstrator'), $row3[9]);
        $this->assertEquals($application3->student->getDateOf('StudentConfirm', $application3->request, 'Demonstrator'), $row3[10]);
        $this->assertEquals($application3->student->getDateOf('AcademicAcceptsStudent', $application3->request, 'Marker'), $row3[11]);
        $this->assertEquals($application3->student->getDateOf('StudentConfirm', $application3->request, 'Marker'), $row3[12]);
        $this->assertEquals($application3->student->getDateOf('AcademicAcceptsStudent', $application3->request, 'Tutor'), $row3[13]);
        $this->assertEquals($application3->student->getDateOf('StudentConfirm', $application3->request, 'Tutor'), $row3[14]);
    }

    /** @test */
    public function can_export_output_3()
    {
        //Confirmed students
        $admin = factory(User::class)->states('admin')->create();
        $student1 = factory(User::class)->create(['surname' => 'Adler']);
        $student2 = factory(User::class)->create(['surname' => 'Bea']);
        $confirmedApplication1 = factory(DemonstratorApplication::class)->create(['student_id' => $student1->id, 'is_accepted' => true, 'student_responded' => true, 'student_confirms' => true]);
        $confirmedApplication2 = factory(DemonstratorApplication::class)->create(['student_id' => $student2->id, 'is_accepted' => true, 'student_responded' => true, 'student_confirms' => true]);
        $unconfirmedApplication = factory(DemonstratorApplication::class)->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.output3.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=output3.xlsx');
        $file = $response->getFile();
        $data = (new ExcelSheet)->import($file->getPathname());
        $headings = array_shift($data);
        $row1 = array_shift($data);
        $row2 = array_shift($data);
        $this->assertEquals($confirmedApplication1->student->fullName, $row1[0]);
        $this->assertEquals($confirmedApplication1->student->email, $row1[1]);
        $this->assertEquals($confirmedApplication1->student->getTotalConfirmedHours(), $row1[4]);
        $this->assertEquals($confirmedApplication2->student->fullName, $row2[0]);
        $this->assertEquals($confirmedApplication2->student->email, $row2[1]);
        $this->assertEquals($confirmedApplication2->student->getTotalConfirmedHours(), $row2[4]);
    }

    /** @test */
    public function can_export_output_4()
    {
        //Accepted students by course
        $admin = factory(User::class)->states('admin')->create();
        $course1 = factory(Course::class)->create(['title' => 'ABC']);
        $staff1 = factory(User::class)->states('staff')->create();
        $staff1->courses()->attach($course1->id);

        $course2 = factory(Course::class)->create(['title' => 'DEF']);
        $staff2 = factory(User::class)->states('staff')->create();
        $staff2->courses()->attach($course2->id);

        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $course1->id, 'staff_id' => $staff1->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $course2->id, 'staff_id' => $staff2->id]);

        $student1 = factory(User::class)->create();
        $student2 = factory(User::class)->create();
        $student3 = factory(User::class)->create();
        $confirmedApplication1 = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id, 'student_id' => $student1->id, 'is_accepted' => true, 'student_responded' => true, 'student_confirms' => true]);
        $confirmedApplication2 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id, 'student_id' => $student2->id, 'is_accepted' => true, 'student_responded' => true, 'student_confirms' => true]);
        $confirmedApplication3 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id, 'student_id' => $student3->id, 'is_accepted' => true, 'student_responded' => true, 'student_confirms' => true]);

        $response = $this->actingAs($admin)->get(route('admin.reports.output4.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=output4.xlsx');
        $file = $response->getFile();
        $data = (new ExcelSheet)->import($file->getPathname());
        $headings = array_shift($data);
        $row1 = array_shift($data);
        $row2 = array_shift($data);
        $row3 = array_shift($data);
        $this->assertEquals($confirmedApplication1->request->course->code, $row1[0]);
        $this->assertEquals($confirmedApplication1->request->course->title, $row1[1]);
        $this->assertEquals($confirmedApplication1->request->staff->fullName, $row1[2]);
        $this->assertEquals($confirmedApplication1->request->staff->email, $row1[3]);
        $this->assertEquals($confirmedApplication1->student->fullName, $row1[4]);
        $this->assertEquals($confirmedApplication1->student->email, $row1[5]);
        $this->assertEquals($confirmedApplication1->student->getDateOf('AcademicAcceptsStudent', $confirmedApplication1->request, 'Demonstrator'), $row1[6]);
        $this->assertEquals($confirmedApplication1->student->getDateOf('StudentConfirm', $confirmedApplication1->request, 'Demonstrator'), $row1[7]);
        $this->assertEquals($confirmedApplication1->student->getDateOf('AcademicAcceptsStudent', $confirmedApplication1->request, 'Marker'), $row1[8]);
        $this->assertEquals($confirmedApplication1->student->getDateOf('StudentConfirm', $confirmedApplication1->request, 'Marker'), $row1[9]);
        $this->assertEquals($confirmedApplication1->student->getDateOf('AcademicAcceptsStudent', $confirmedApplication1->request, 'Tutor'), $row1[10]);
        $this->assertEquals($confirmedApplication1->student->getDateOf('StudentConfirm', $confirmedApplication1->request, 'Tutor'), $row1[11]);

        $this->assertEquals($confirmedApplication2->request->course->code, $row2[0]);
        $this->assertEquals($confirmedApplication2->request->course->title, $row2[1]);
        $this->assertEquals($confirmedApplication2->request->staff->fullName, $row2[2]);
        $this->assertEquals($confirmedApplication2->request->staff->email, $row2[3]);
        $this->assertEquals($confirmedApplication2->student->fullName, $row2[4]);
        $this->assertEquals($confirmedApplication2->student->email, $row2[5]);
        $this->assertEquals($confirmedApplication2->student->getDateOf('AcademicAcceptsStudent', $confirmedApplication2->request, 'Demonstrator'), $row2[6]);
        $this->assertEquals($confirmedApplication2->student->getDateOf('StudentConfirm', $confirmedApplication2->request, 'Demonstrator'), $row2[7]);
        $this->assertEquals($confirmedApplication2->student->getDateOf('AcademicAcceptsStudent', $confirmedApplication2->request, 'Marker'), $row2[8]);
        $this->assertEquals($confirmedApplication2->student->getDateOf('StudentConfirm', $confirmedApplication2->request, 'Marker'), $row2[9]);
        $this->assertEquals($confirmedApplication2->student->getDateOf('AcademicAcceptsStudent', $confirmedApplication2->request, 'Tutor'), $row2[10]);
        $this->assertEquals($confirmedApplication2->student->getDateOf('StudentConfirm', $confirmedApplication2->request, 'Tutor'), $row2[11]);

        $this->assertEquals($confirmedApplication3->request->course->code, $row3[0]);
        $this->assertEquals($confirmedApplication3->request->course->title, $row3[1]);
        $this->assertEquals($confirmedApplication3->request->staff->fullName, $row3[2]);
        $this->assertEquals($confirmedApplication3->request->staff->email, $row3[3]);
        $this->assertEquals($confirmedApplication3->student->fullName, $row3[4]);
        $this->assertEquals($confirmedApplication3->student->email, $row3[5]);
        $this->assertEquals($confirmedApplication3->student->getDateOf('AcademicAcceptsStudent', $confirmedApplication3->request, 'Demonstrator'), $row3[6]);
        $this->assertEquals($confirmedApplication3->student->getDateOf('StudentConfirm', $confirmedApplication3->request, 'Demonstrator'), $row3[7]);
        $this->assertEquals($confirmedApplication3->student->getDateOf('AcademicAcceptsStudent', $confirmedApplication3->request, 'Marker'), $row3[8]);
        $this->assertEquals($confirmedApplication3->student->getDateOf('StudentConfirm', $confirmedApplication3->request, 'Marker'), $row3[9]);
        $this->assertEquals($confirmedApplication3->student->getDateOf('AcademicAcceptsStudent', $confirmedApplication3->request, 'Tutor'), $row3[10]);
        $this->assertEquals($confirmedApplication3->student->getDateOf('StudentConfirm', $confirmedApplication3->request, 'Tutor'), $row3[11]);
    }

    /** @test */
    public function can_export_output_5()
    {
        $admin = factory(User::class)->states('admin')->create();
        $course1 = factory(Course::class)->create(['title' => 'ABC']);
        $course2 = factory(Course::class)->create(['title' => 'DEF']);
        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $course1->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $course2->id]);

        $response = $this->actingAs($admin)->get(route('admin.reports.output5.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=output5.xlsx');
        $file = $response->getFile();
        $data = (new ExcelSheet)->import($file->getPathname());
        $headings = array_shift($data);
        $row1 = array_shift($data);
        $row2 = array_shift($data);
        $this->assertEquals($request1->course->code, $row1[0]);
        $this->assertEquals($request1->course->title, $row1[1]);
        $this->assertEquals($request1->staff->fullName, $row1[2]);
        $this->assertEquals($request1->staff->email, $row1[3]);
        $this->assertEquals($request1->type, $row1[4]);
        $this->assertEquals($request1->getFormattedStartDate(), $row1[5]);
        $this->assertEquals($request2->course->code, $row2[0]);
        $this->assertEquals($request2->course->title, $row2[1]);
        $this->assertEquals($request2->staff->fullName, $row2[2]);
        $this->assertEquals($request2->staff->email, $row2[3]);
        $this->assertEquals($request2->type, $row2[4]);
        $this->assertEquals($request2->getFormattedStartDate(), $row2[5]);
    }

    /** @test */
    public function can_export_output_6()
    {
        $admin = factory(User::class)->states('admin')->create();
        $course1 = factory(Course::class)->create(['title' => 'ABC']);
        $course2 = factory(Course::class)->create(['title' => 'DEF']);
        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $course1->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $course2->id]);
        $application1 = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id, 'created_at' => new Carbon('Last week')]);
        $application2 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id, 'created_at' => new Carbon('Last week')]);

        $response = $this->actingAs($admin)->get(route('admin.reports.output6.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=output6.xlsx');
        $file = $response->getFile();
        $data = (new ExcelSheet)->import($file->getPathname());
        $headings = array_shift($data);
        $row1 = array_shift($data);
        $row2 = array_shift($data);
        $this->assertEquals($application1->request->course->code, $row1[0]);
        $this->assertEquals($application1->request->course->title, $row1[1]);
        $this->assertEquals($application1->request->staff->fullName, $row1[2]);
        $this->assertEquals($application1->request->staff->email, $row1[3]);
        $this->assertEquals($application1->request->type, $row1[4]);
        $this->assertEquals($application1->request->getFormattedStartDate(), $row1[5]);
        $this->assertEquals($application2->request->course->code, $row2[0]);
        $this->assertEquals($application2->request->course->title, $row2[1]);
        $this->assertEquals($application2->request->staff->fullName, $row2[2]);
        $this->assertEquals($application2->request->staff->email, $row2[3]);
        $this->assertEquals($application2->request->type, $row2[4]);
        $this->assertEquals($application2->request->getFormattedStartDate(), $row2[5]);
    }

    /** @test */
    public function can_export_output_7()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $student1 = factory(User::class)->create(['surname' => 'Adler']);
        $student2 = factory(User::class)->create(['surname' => 'Bea']);
        $application1 = factory(DemonstratorApplication::class)->create(['student_id' => $student1->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['student_id' => $student2->id]);

        $response = $this->actingAs($admin)->get(route('admin.reports.output7.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=output7.xlsx');
        $file = $response->getFile();
        $data = (new ExcelSheet)->import($file->getPathname());
        $headings = array_shift($data);
        $row1 = array_shift($data);
        $row2 = array_shift($data);
        $this->assertEquals($application1->student->fullName, $row1[0]);
        $this->assertEquals($application1->request->course->code, $row1[1]);
        $this->assertEquals($application1->request->course->title, $row1[2]);
        $this->assertEquals($application1->request->staff->fullName, $row1[3]);
        $this->assertEquals($application1->request->type, $row1[4]);
        $this->assertEquals($application2->student->fullName, $row2[0]);
        $this->assertEquals($application2->request->course->code, $row2[1]);
        $this->assertEquals($application2->request->course->title, $row2[2]);
        $this->assertEquals($application2->request->staff->fullName, $row2[3]);
        $this->assertEquals($application2->request->type, $row2[4]);
    }
}
