<?php
// @codingStandardsIgnoreFile
namespace Tests\Unit;

use App\Course;
use App\DemonstratorRequest;
use App\Importers\DemonstratorRequestImporter;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_convert_spreadsheet_data_to_correct_models () {
        $data = [
            [
                'ENG','1003','Analogue Electronics 1','Scott Roy','5','42','','','','','','','','','','Demonstrator','','','','Open to year 4 students  - on the proviso it doesn’t have a detrimental effect on studies','1',
            ],
            [
                'ENG','1021','Electronic Engineering 1X','Scott Roy','','','','4','50','','','','','','','Tutor','','','',"Background in a cognate subject eg 'electronics', 'electrical', 'biomedical', computer science or physics (not 'Mech', aero or 'civil')",'1 & 2'
            ]
        ];
        $errors = (new DemonstratorRequestImporter())->import($data);
        $this->assertEquals(2, Course::count());
        $courses = Course::all();
        $this->assertEquals('ENG1003', $courses[0]->code);
        $this->assertEquals('ENG1021', $courses[1]->code);
        $this->assertEquals('Analogue Electronics 1', $courses[0]->title);
        $this->assertEquals('Electronic Engineering 1X', $courses[1]->title);

        $this->assertEquals(1, User::count());
        $user = User::first();
        $this->assertEquals('Scott Roy', $user->username);

        $this->assertEquals(2, DemonstratorRequest::count());
        $requests = DemonstratorRequest::all();
        $this->assertEquals('Demonstrator', $requests[0]->type);
        $this->assertEquals(5, $requests[0]->demonstrators_needed);
        $this->assertEquals(42, $requests[0]->hours_needed);
        $this->assertEquals('Open to year 4 students  - on the proviso it doesn’t have a detrimental effect on studies', $requests[0]->skills);
        $this->assertTrue($requests[0]->semester_1);
        $this->assertFalse($requests[0]->semester_2);
        $this->assertFalse($requests[0]->semester_3);

        $this->assertEquals('Tutor', $requests[1]->type);
        $this->assertEquals(4, $requests[1]->demonstrators_needed);
        $this->assertEquals(50, $requests[1]->hours_needed);
        $this->assertEquals("Background in a cognate subject eg 'electronics', 'electrical', 'biomedical', computer science or physics (not 'Mech', aero or 'civil')", $requests[1]->skills);
        $this->assertTrue($requests[1]->semester_1);
        $this->assertTrue($requests[1]->semester_2);
        $this->assertFalse($requests[1]->semester_3);

    }
}