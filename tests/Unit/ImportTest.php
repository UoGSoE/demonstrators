<?php

// @codingStandardsIgnoreFile

namespace Tests\Unit;

use App\Models\Course;
use App\Models\DemonstratorRequest;
use App\Importers\DemonstratorRequestImporter;
use App\Models\User;
use Carbon\Carbon;
use Datetime;
use Tests\TestCase;

class ImportTest extends TestCase
{
    /** @test */
    public function can_convert_spreadsheet_data_to_correct_models()
    {
        $data = [
            [
                'ENG', '1003', new DateTime('10/12/2017'), 'Analogue Electronics 1', 'Scott Roy', 'sct9r', 'sct.roy@example.com', '', '', '', '5', '42', '3', '', '', '', '', '', '', '', 'Demonstrator', '', '', '', 'Open to year 4 students  - on the proviso it doesn’t have a detrimental effect on studies', '1',
            ],
            [
                'ENG', '1021', new DateTime('12/17/2017'), 'Electronic Engineering 1X', 'Scott Roy', 'sct9r', 'sct.roy@example.com', '', '', '', '', '', '', '4', '50', '', '', '', '', '', 'Tutor', '', '', '', "Background in a cognate subject eg 'electronics', 'electrical', 'biomedical', computer science or physics (not 'Mech', aero or 'civil')", '1 & 2',
            ],
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
        $this->assertEquals('sct9r', $user->username);
        $this->assertEquals('sct.roy@example.com', $user->email);

        $this->assertEquals(2, DemonstratorRequest::count());
        $requests = DemonstratorRequest::all();
        $this->assertEquals('Demonstrator', $requests[0]->type);
        $this->assertEquals('2017-10-12', $requests[0]->start_date);
        $this->assertEquals(5, $requests[0]->demonstrators_needed);
        $this->assertEquals(42, $requests[0]->hours_needed);
        $this->assertEquals(3, $requests[0]->hours_training);
        $this->assertEquals('Open to year 4 students  - on the proviso it doesn’t have a detrimental effect on studies', $requests[0]->skills);
        $this->assertTrue($requests[0]->semester_1);
        $this->assertFalse($requests[0]->semester_2);
        $this->assertFalse($requests[0]->semester_3);

        $this->assertEquals('Tutor', $requests[1]->type);
        $this->assertEquals('2017-12-17', $requests[1]->start_date);
        $this->assertEquals(4, $requests[1]->demonstrators_needed);
        $this->assertEquals(50, $requests[1]->hours_needed);
        $this->assertEquals("Background in a cognate subject eg 'electronics', 'electrical', 'biomedical', computer science or physics (not 'Mech', aero or 'civil')", $requests[1]->skills);
        $this->assertTrue($requests[1]->semester_1);
        $this->assertTrue($requests[1]->semester_2);
        $this->assertFalse($requests[1]->semester_3);
    }
}
