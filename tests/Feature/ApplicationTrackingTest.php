<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Job;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_apply_for_job_and_duplicate_application_is_prevented(): void
    {
        $studentUser = User::create([
            'name' => 'Charlie Student',
            'email' => 'charlie@student.edu',
            'password' => 'secret123',
            'role' => 'student',
        ]);
        $student = Student::create(['user_id' => $studentUser->id]);

        $companyUser = User::create([
            'name' => 'Tech Corp',
            'email' => 'hr@techcorp.com',
            'password' => 'secret123',
            'role' => 'company',
        ]);
        $company = Company::create(['user_id' => $companyUser->id, 'name' => 'Tech Corp']);

        $job = Job::create([
            'company_id' => $company->id,
            'title' => 'Backend Engineering Intern',
            'description' => 'Build high throughput REST APIs',
            'job_type' => 'internship',
            'is_active' => true,
        ]);

        $token = $studentUser->createToken('test_token')->plainTextToken;

        // 1. Submit initial application
        $response1 = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/applications', [
                'job_id' => $job->id,
                'cover_letter_url' => 'https://storage.com/cover.pdf',
                'expected_salary' => '₹40,000/month',
            ]);

        $response1->assertStatus(201)
                  ->assertJsonPath('success', true)
                  ->assertJsonPath('data.application.status', 'applied');

        $this->assertDatabaseHas('applications', [
            'student_id' => $student->id,
            'job_id' => $job->id,
            'status' => 'applied',
        ]);

        // Audit log created
        $this->assertDatabaseHas('application_status_history', [
            'status' => 'applied',
            'changed_by' => $studentUser->id,
        ]);

        // 2. Submit duplicate application -> Should be rejected
        $response2 = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/applications', [
                'job_id' => $job->id,
            ]);

        $response2->assertStatus(400)
                  ->assertJsonPath('success', false);
    }

    public function test_interview_final_round_cleared_updates_application_to_selected(): void
    {
        $studentUser = User::create([
            'name' => 'David',
            'email' => 'david@student.edu',
            'password' => 'secret123',
            'role' => 'student',
        ]);
        $student = Student::create(['user_id' => $studentUser->id]);

        $companyUser = User::create([
            'name' => 'Apex Systems',
            'email' => 'jobs@apex.com',
            'password' => 'secret123',
            'role' => 'company',
        ]);
        $company = Company::create(['user_id' => $companyUser->id, 'name' => 'Apex Systems']);

        $job = Job::create([
            'company_id' => $company->id,
            'title' => 'DevOps Intern',
            'description' => 'Docker & Kubernetes',
            'job_type' => 'internship',
            'is_active' => true,
        ]);

        $application = \App\Models\Application::create([
            'student_id' => $student->id,
            'job_id' => $job->id,
            'status' => 'applied',
            'applied_at' => now(),
        ]);

        $companyToken = $companyUser->createToken('company_token')->plainTextToken;

        // Schedule interview round
        $schedResponse = $this->withHeader('Authorization', "Bearer {$companyToken}")
            ->postJson("/api/v1/applications/{$application->id}/interviews", [
                'round_name' => 'Technical Deep-Dive',
                'interview_type' => 'online',
                'interviewer' => 'Lead Architect',
            ]);

        $schedResponse->assertStatus(201);
        $interviewId = $schedResponse->json('data.interview.id');

        // Application status should now be 'interview'
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'interview',
        ]);

        // Submit final round result -> Cleared
        $resultResponse = $this->withHeader('Authorization', "Bearer {$companyToken}")
            ->putJson("/api/v1/interviews/{$interviewId}", [
                'result' => 'cleared',
                'is_final_round' => true,
                'notes' => 'Candidate demonstrated excellent knowledge.',
            ]);

        $resultResponse->assertStatus(200);

        // Application status should automatically become 'selected'
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'selected',
        ]);
    }
}
