<?php
// @codingStandardsIgnoreFile
namespace Tests\Feature;

use App\User;
use App\Course;
use Tests\TestCase;
use App\DemonstratorRequest;
use App\DemonstratorApplication;

class NewAcademicSessionTest extends TestCase
{
    /** @test */
    public function new_academic_session_removes_all_applicants_and_students_with_expired_contracts ()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $expiredStudent = factory(User::class)->states('student')->create([
            'has_contract' => true,
            'contract_end' => now()->subDays(2)->format('Y-m-d')
        ]);
        $validStudent = factory(User::class)->states('student')->create([
            'has_contract' => true,
            'contract_end' => now()->addDays(2)->format('Y-m-d')
        ]);
        $course = factory(Course::class)->create();
        $request = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'start_date' => now()->subYear()->format('Y-m-d')]);
        $application = factory(DemonstratorApplication::class)->create(['request_id' => $request->id]);

        $response = $this->actingAs($admin)->post(route('admin.system.next_year'));

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.system.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $expiredStudent->id
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $validStudent->id
        ]);
        $this->assertDatabaseMissing('demonstrator_requests', [
            'id' => $request->id,
            'start_date' => now()->subYear()->format('Y-m-d')
        ]);
        $this->assertCount(0, DemonstratorApplication::all());
    }

    /** @test */
    public function can_provide_a_contract_expiration_date_threshold()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $expiredStudent = factory(User::class)->states('student')->create([
            'has_contract' => true,
            'contract_end' => now()->subDays(5)->format('Y-m-d')
        ]);
        $validStudent = factory(User::class)->states('student')->create([
            'has_contract' => true,
            'contract_end' => now()->addDays(2)->format('Y-m-d')
        ]);
        $validStudent2 = factory(User::class)->states('student')->create([
            'has_contract' => true,
            'contract_end' => now()->subDays(1)->format('Y-m-d')
        ]);
        $course = factory(Course::class)->create();
        $request = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'start_date' => now()->subYear()->format('Y-m-d')]);
        $application = factory(DemonstratorApplication::class)->create(['request_id' => $request->id]);

        $response = $this->actingAs($admin)->post(route('admin.system.next_year'), [
            'contract_expiration' => now()->subDays(2)->format('Y-m-d')
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.system.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $expiredStudent->id
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $validStudent->id
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $validStudent2->id
        ]);
        $this->assertDatabaseMissing('demonstrator_requests', [
            'id' => $request->id,
            'start_date' => now()->subYear()->format('Y-m-d')
        ]);
        $this->assertCount(0, DemonstratorApplication::all());
    }


    /** @test */
    public function can_provide_a_request_start_date_threshold()
    {
        $this->withoutExceptionHandling();
        $admin = factory(User::class)->states('admin')->create();
        $expiredStudent = factory(User::class)->states('student')->create([
            'has_contract' => true,
            'contract_end' => now()->subDays(5)->format('Y-m-d')
        ]);
        $validStudent = factory(User::class)->states('student')->create([
            'has_contract' => true,
            'contract_end' => now()->addDays(2)->format('Y-m-d')
        ]);
        $course = factory(Course::class)->create();
        $request = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'start_date' => now()->subYear()->format('Y-m-d')]);
        $request2 = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'start_date' => now()->subDays(2)->format('Y-m-d')]);
        $application = factory(DemonstratorApplication::class)->create(['request_id' => $request->id]);
        $application2 = factory(DemonstratorApplication::class)->create(['request_id' => $request2->id]);

        $response = $this->actingAs($admin)->post(route('admin.system.next_year'), [
            'request_start' => now()->subDays(3)->format('Y-m-d')
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.system.index'));

        $this->assertDatabaseMissing('users', [
            'id' => $expiredStudent->id
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $validStudent->id
        ]);
        $this->assertDatabaseMissing('demonstrator_requests', [
            'id' => $request->id,
            'start_date' => now()->subYear()->format('Y-m-d')
        ]);
        $this->assertDatabaseHas('demonstrator_requests', [
            'id' => $request2->id,
            'start_date' => now()->subDays(2)->format('Y-m-d')
        ]);
        $this->assertCount(1, DemonstratorApplication::all());
    }
}
