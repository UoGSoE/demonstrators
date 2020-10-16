<?php

// @codingStandardsIgnoreFile

namespace Tests\Feature;

use App\Models\Course;
use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Models\EmailLog;
use App\Models\User;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    /** @test */
    public function can_remove_all_students_with_expired_contracts_before_provided_date()
    {
        $admin = User::factory()->admin()->create();
        $expiredStudent = User::factory()->student()->create(['has_contract' => true, 'contract_end' => now()->subDays(5)->format('Y-m-d')]);
        $validStudent = User::factory()->student()->create(['has_contract' => true, 'contract_end' => now()->addDays(2)->format('Y-m-d')]);
        $validStudent2 = User::factory()->student()->create(['has_contract' => true, 'contract_end' => now()->subDays(1)->format('Y-m-d')]);
        $noContractStudent = User::factory()->student()->create(['has_contract' => false, 'contract_end' => null]);

        $expiredStudentApplication = DemonstratorApplication::factory()->create(['student_id' => $expiredStudent->id]);
        $validStudentApplication = DemonstratorApplication::factory()->create(['student_id' => $validStudent->id]);
        $validStudent2Application = DemonstratorApplication::factory()->create(['student_id' => $validStudent2->id]);
        $noContractStudentApplication = DemonstratorApplication::factory()->create(['student_id' => $noContractStudent->id]);

        $expiredStudentEmailLog = EmailLog::factory()->create(['user_id' => $expiredStudent->id]);
        $validStudentEmailLog = EmailLog::factory()->create(['user_id' => $validStudent->id]);
        $validStudent2EmailLog = EmailLog::factory()->create(['user_id' => $validStudent2->id]);
        $noContractStudentEmailLog = EmailLog::factory()->create(['user_id' => $noContractStudent->id]);

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
        $admin = User::factory()->admin()->create();
        $oldRequest = DemonstratorRequest::factory()->create(['start_date' => now()->subYear()->format('Y-m-d')]);
        $currentRequest = DemonstratorRequest::factory()->create(['start_date' => now()->subDays(2)->format('Y-m-d')]);
        $currentRequest2 = DemonstratorRequest::factory()->create(['start_date' => now()->subDays(3)->format('Y-m-d')]);
        $oldRequestApplication = DemonstratorApplication::factory()->create(['request_id' => $oldRequest->id]);
        $currentRequestApplication = DemonstratorApplication::factory()->create(['request_id' => $currentRequest->id]);
        $currentRequest2Application = DemonstratorApplication::factory()->create(['request_id' => $currentRequest2->id]);

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
