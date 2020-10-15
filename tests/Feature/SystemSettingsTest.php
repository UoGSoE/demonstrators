<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Course;
use App\DemonstratorApplication;
use App\DemonstratorRequest;
use App\EmailLog;
use App\User;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    /** @test */
    public function can_remove_all_students_with_expired_contracts_before_provided_date()
    {
        $admin = factory(User::class)->states('admin')->create();
        $expiredStudent = factory(User::class)->states('student')->create(['has_contract' => true, 'contract_end' => now()->subDays(5)->format('Y-m-d')]);
        $validStudent = factory(User::class)->states('student')->create(['has_contract' => true, 'contract_end' => now()->addDays(2)->format('Y-m-d')]);
        $validStudent2 = factory(User::class)->states('student')->create(['has_contract' => true, 'contract_end' => now()->subDays(1)->format('Y-m-d')]);
        $noContractStudent = factory(User::class)->states('student')->create(['has_contract' => false, 'contract_end' => null]);

        $expiredStudentApplication = factory(DemonstratorApplication::class)->create(['student_id' => $expiredStudent->id]);
        $validStudentApplication = factory(DemonstratorApplication::class)->create(['student_id' => $validStudent->id]);
        $validStudent2Application = factory(DemonstratorApplication::class)->create(['student_id' => $validStudent2->id]);
        $noContractStudentApplication = factory(DemonstratorApplication::class)->create(['student_id' => $noContractStudent->id]);

        $expiredStudentEmailLog = factory(EmailLog::class)->create(['user_id' => $expiredStudent->id]);
        $validStudentEmailLog = factory(EmailLog::class)->create(['user_id' => $validStudent->id]);
        $validStudent2EmailLog = factory(EmailLog::class)->create(['user_id' => $validStudent2->id]);
        $noContractStudentEmailLog = factory(EmailLog::class)->create(['user_id' => $noContractStudent->id]);

        $response = $this->actingAs($admin)->post(route('admin.system.expired_contracts'), [
            'contract_expiration' => now()->subDays(2)->format('Y-m-d'),
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.system.index'));

        $this->assertDatabaseMissing('users', ['id' => $expiredStudent->id]);
        $this->assertDatabaseMissing('demonstrator_applications', ['student_id' => $expiredStudent->id]);
        $this->assertDatabaseMissing('email_logs', ['user_id' => $expiredStudent->id]);

        $this->assertDatabaseHas('users', ['id' => $validStudent->id]);
        $this->assertDatabaseHas('demonstrator_applications', ['student_id' => $validStudent->id]);
        $this->assertDatabaseHas('email_logs', ['user_id' => $validStudent->id]);

        $this->assertDatabaseHas('users', ['id' => $validStudent2->id]);
        $this->assertDatabaseHas('demonstrator_applications', ['student_id' => $validStudent2->id]);
        $this->assertDatabaseHas('email_logs', ['user_id' => $validStudent2->id]);

        $this->assertDatabaseHas('users', ['id' => $noContractStudent->id]);
        $this->assertDatabaseHas('demonstrator_applications', ['student_id' => $noContractStudent->id]);
        $this->assertDatabaseHas('email_logs', ['user_id' => $noContractStudent->id]);
    }

    /** @test */
    public function can_reset_all_requests_before_provided_date()
    {
        $admin = factory(User::class)->states('admin')->create();
        $oldRequest = factory(DemonstratorRequest::class)->create(['start_date' => now()->subYear()->format('Y-m-d')]);
        $currentRequest = factory(DemonstratorRequest::class)->create(['start_date' => now()->subDays(2)->format('Y-m-d')]);
        $currentRequest2 = factory(DemonstratorRequest::class)->create(['start_date' => now()->subDays(3)->format('Y-m-d')]);
        $oldRequestApplication = factory(DemonstratorApplication::class)->create(['request_id' => $oldRequest->id]);
        $currentRequestApplication = factory(DemonstratorApplication::class)->create(['request_id' => $currentRequest->id]);
        $currentRequest2Application = factory(DemonstratorApplication::class)->create(['request_id' => $currentRequest2->id]);

        $response = $this->actingAs($admin)->post(route('admin.system.reset_requests'), [
            'request_start' => now()->subDays(3)->format('Y-m-d'),
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.system.index'));

        $this->assertDatabaseHas('demonstrator_requests', ['id' => $oldRequest->id, 'start_date' => null]);
        $this->assertDatabaseMissing('demonstrator_applications', ['id' => $oldRequestApplication->id, 'request_id' => $oldRequest->id]);

        $this->assertDatabaseHas('demonstrator_requests', ['id' => $currentRequest->id, 'start_date' => now()->subDays(2)->format('Y-m-d')]);
        $this->assertDatabaseHas('demonstrator_applications', ['id' => $currentRequestApplication->id, 'request_id' => $currentRequest->id]);

        $this->assertDatabaseHas('demonstrator_requests', ['id' => $currentRequest2->id, 'start_date' => now()->subDays(3)->format('Y-m-d')]);
        $this->assertDatabaseHas('demonstrator_applications', ['id' => $currentRequest2Application->id, 'request_id' => $currentRequest2->id]);
    }
}
