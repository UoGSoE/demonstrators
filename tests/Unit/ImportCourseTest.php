<?php

// @codingStandardsIgnoreFile

namespace Tests\Unit;

use App\Models\Course;
use App\Importers\CourseImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportCourseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_upload_spreadsheet_of_courses()
    {
        $this->withoutExceptionHandling();
        $uploadedCourse = Course::factory()->create();
        $data = [
            [
                $uploadedCourse->code, $uploadedCourse->title,
            ],
            [
                'ENG1003', 'Test Course 1',
            ],
            [
                'ENG1021', 'Test Course 2',
            ],
            [
                '', 'Test Course 3',
            ],
            [
                'ENG1234', '',
            ],
        ];
        $errors = (new CourseImporter())->import($data);
        $this->assertEquals(3, Course::count());
        $this->assertContains("Course code '$uploadedCourse->code' already exists in the database (found on row 1).", $errors);
        $this->assertContains('Course code missing (row 4).', $errors);
        $this->assertContains('Course title missing (row 5).', $errors);
    }
}
