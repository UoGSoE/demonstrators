<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CourseImportTest extends TestCase
{
    /** @test */
    public function can_start_uploading_a_spreadsheet_of_courses()
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.courses.import.create'));
        $response->assertStatus(200);
        $response->assertSee('Upload New Courses');
    }
}
