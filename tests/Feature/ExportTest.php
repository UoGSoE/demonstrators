<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\User;
use App\Course;
use Carbon\Carbon;
use Tests\TestCase;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use Ohffs\SimpleSpout\ExcelSheet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_export_output_3 ()
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
        $response->assertHeader('content-disposition', 'attachment; filename="output3.xlsx"');
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
        $response->assertHeader('content-disposition', 'attachment; filename="output4.xlsx"');
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
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $course1 = factory(Course::class)->create(['title' => 'ABC']);
        $course2 = factory(Course::class)->create(['title' => 'DEF']);
        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $course1->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $course2->id]);
        
        $response = $this->actingAs($admin)->get(route('admin.reports.output5.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename="output5.xlsx"');
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
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $course1 = factory(Course::class)->create(['title' => 'ABC']);
        $course2 = factory(Course::class)->create(['title' => 'DEF']);
        $request1 = factory(DemonstratorRequest::class)->create(['course_id' => $course1->id]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $course2->id]);
        $application1 = factory(DemonstratorApplication::class)->create(['request_id' => $request1->id, 'created_at' => new Carbon('Last week')]);
        $application2 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id, 'created_at' => new Carbon('Last week')]);

        $response = $this->actingAs($admin)->get(route('admin.reports.output6.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename="output6.xlsx"');
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
        $response->assertHeader('content-disposition', 'attachment; filename="output7.xlsx"');
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
