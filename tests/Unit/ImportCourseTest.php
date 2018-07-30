<?php
// @codingStandardsIgnoreFile
namespace Tests\Unit;

use App\Course;
use Tests\TestCase;
use App\Importers\CourseImporter;

class ImportCourseTest extends TestCase
{
    /** @test */
    public function can_upload_spreadsheet_of_courses ()
    {
        $uploadedCourse = factory(Course::class)->create();
        $data = [
            [
                $uploadedCourse->code, $uploadedCourse->title
            ],
            [
                'ENG1003', 'Test Course 1'
            ],
            [
                'ENG1021', 'Test Course 2',
            ],
            [
                '', 'Test Course 3',
            ],
            [
                'ENG1234', ''
            ]
        ];
        $errors = (new CourseImporter())->import($data);
        $this->assertEquals(3, Course::count());
        $this->assertContains("Course code '$uploadedCourse->code' already exists in the database (found on row 1).", $errors);
        $this->assertContains('Course code missing (row 4).', $errors);
        $this->assertContains('Course title missing (row 5).', $errors);
    }
}
