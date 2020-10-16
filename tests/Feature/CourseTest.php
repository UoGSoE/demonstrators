<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\Course;
use App\Models\DemonstratorRequest;
use App\Models\User;
use Tests\TestCase;

class CourseTest extends TestCase
{
    /** @test */
    public function can_view_a_list_of_all_courses()
    {
        $admin = User::factory()->admin()->create();
        $courses = Course::factory()->count(4)->create();

        $response = $this->actingAs($admin)->get(route('admin.courses.index'));
        $response->assertStatus(200);
        $response->assertSee($courses[0]->title);
        $response->assertSee($courses[1]->title);
        $response->assertSee($courses[2]->title);
    }

    /** @test */
    public function can_add_a_new_course()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.courses.create'));
        $response->assertStatus(200);
        $response->assertSee('Add New Course');
    }

    /** @test */
    public function can_store_a_new_course()
    {
        $this->withoutExceptionHandling();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.courses.store'), [
            'code' => 'ENG1234',
            'title' => 'Medical Engineering 101',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseHas('courses', ['code' => 'ENG1234', 'title' => 'Medical Engineering 101']);
    }

    /** @test */
    public function cannot_store_a_course_with_a_used_code()
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.courses.store'), [
            'code' => $course->code,
            'title' => 'Medical Engineering 101',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function can_edit_a_course()
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.courses.edit', $course->id));
        $response->assertStatus(200);
        $response->assertSee("Edit Course: $course->fullTitle");
    }

    /** @test */
    public function warning_is_shown_when_staff_edits_a_course_that_has_requests()
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();
        $request = DemonstratorRequest::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($admin)->get(route('admin.courses.edit', $course->id));
        $response->assertStatus(200);
        $response->assertSee("Edit Course: $course->fullTitle");
        $response->assertSee('Please be aware that you are editing a course that is assigned to a staff member or has active requests.');
    }

    /** @test */
    public function warning_is_not_shown_when_staff_edits_a_course_that_has_requests()
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.courses.edit', $course->id));
        $response->assertStatus(200);
        $response->assertSee("Edit Course: $course->fullTitle");
        $response->assertDontSee('Please be aware that you are editing a course that is assigned to a staff member or has active requests.');
    }

    /** @test */
    public function can_update_a_course()
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['code' => 'ENG1234', 'title' => 'Old title']);

        $response = $this->actingAs($admin)->post(route('admin.courses.update', $course->id), [
            'code' => 'ENG1025',
            'title' => 'New title',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect(route('admin.courses.index'));
        $this->assertDatabaseHas('courses', ['code' => 'ENG1025', 'title' => 'New title']);
        $this->assertDatabaseMissing('courses', ['code' => 'ENG1234', 'title' => 'Old title']);
    }

    /** @test */
    public function cannot_update_a_course_to_a_code_that_is_already_used()
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['code' => 'ENG1234', 'title' => 'Old title']);
        $course2 = Course::factory()->create(['code' => 'ENG1025', 'title' => 'Old title']);

        $response = $this->actingAs($admin)->post(route('admin.courses.update', $course->id), [
            'code' => 'ENG1025',
            'title' => 'New title',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['code']);
        $this->assertDatabaseHas('courses', ['code' => 'ENG1234', 'title' => 'Old title']);
        $this->assertDatabaseHas('courses', ['code' => 'ENG1025', 'title' => 'Old title']);
        $this->assertDatabaseMissing('courses', ['code' => 'ENG1025', 'title' => 'New title']);
    }

    /** @test */
    public function can_delete_a_course()
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['code' => 'ENG1234', 'title' => 'Old title']);

        $response = $this->actingAs($admin)->post(route('admin.courses.destroy', $course->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.courses.index'));
        $response->assertSessionHas(['success_message' => "Deleted course: $course->fullTitle"]);
        $this->assertDatabaseMissing('courses', ['code' => 'ENG1234', 'title' => 'Old title']);
    }

    /** @test */
    public function cannot_delete_a_course_that_is_assigned_to_a_staff_member()
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['code' => 'ENG1234', 'title' => 'Old title']);
        $staff = User::factory()->staff()->create();
        $staff->courses()->attach($course->id);

        $response = $this->actingAs($admin)->post(route('admin.courses.destroy', $course->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.courses.edit', $course->id));
        $response->assertSessionHasErrors(['in_use']);
        $this->assertDatabaseHas('courses', ['code' => 'ENG1234', 'title' => 'Old title']);
    }

    /** @test */
    public function cannot_delete_a_course_that_has_requests()
    {
        $admin = User::factory()->admin()->create();
        $course = Course::factory()->create(['code' => 'ENG1234', 'title' => 'Old title']);
        $request = DemonstratorRequest::factory()->create(['course_id' => $course->id]);

        $response = $this->actingAs($admin)->post(route('admin.courses.destroy', $course->id));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.courses.edit', $course->id));
        $response->assertSessionHasErrors(['in_use']);
        $this->assertDatabaseHas('courses', ['code' => 'ENG1234', 'title' => 'Old title']);
    }
}
