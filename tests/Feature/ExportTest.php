<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use App\DemonstratorApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ohffs\SimpleSpout\ExcelSheet;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_export_output_3 ()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $confirmedApplications = factory(DemonstratorApplication::class, 2)->create(['student_responded' => true, 'student_confirms' => true]);
        $unconfirmedApplication = factory(DemonstratorApplication::class)->create();

        $response = $this->actingAs($admin)->get(route('admin.reports.output3.download'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename="output3.xlsx"');
        $file = $response->getFile();
        $data = (new ExcelSheet)->import($file->getPathname());
        $headings = array_shift($data);
        $row1 = array_shift($data);
        $row2 = array_shift($data);
        $this->assertEquals($confirmedApplications[0]->student->fullName, $row1[0]);
        $this->assertEquals($confirmedApplications[0]->student->email, $row1[1]);
        $this->assertEquals($confirmedApplications[0]->student->getTotalConfirmedHours(), $row1[4]);
        $this->assertEquals($confirmedApplications[1]->student->fullName, $row2[0]);
        $this->assertEquals($confirmedApplications[1]->student->email, $row2[1]);
        $this->assertEquals($confirmedApplications[1]->student->getTotalConfirmedHours(), $row2[4]);
    }
}
