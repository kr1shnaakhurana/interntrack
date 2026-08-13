<?php

namespace App\Services\Applications;

use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Models\Job;
use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class ApplicationService
{
    /**
     * Create a new job application for a student.
     */
    public function apply(Student $student, Job $job, array $data): Application
    {
        return DB::transaction(function () use ($student, $job, $data) {
            $existing = Application::where('student_id', $student->id)
                ->where('job_id', $job->id)
                ->first();

            if ($existing) {
                throw new Exception("Student has already applied to this job listing.");
            }

            $application = Application::create([
                'student_id' => $student->id,
                'job_id' => $job->id,
                'status' => 'applied',
                'applied_at' => now(),
                'cover_letter_url' => $data['cover_letter_url'] ?? null,
                'expected_salary' => $data['expected_salary'] ?? null,
                'current_ctc' => $data['current_ctc'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Initial status history log
            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'status' => 'applied',
                'changed_at' => now(),
                'changed_by' => $student->user_id,
                'remarks' => 'Application submitted.',
            ]);

            // Notify company user if associated
            if ($job->company && $job->company->user_id) {
                Notification::create([
                    'user_id' => $job->company->user_id,
                    'type' => 'application_update',
                    'title' => 'New Application Received',
                    'message' => "A student has applied for '{$job->title}'.",
                    'data' => [
                        'application_id' => $application->id,
                        'job_id' => $job->id,
                        'student_id' => $student->id,
                    ],
                ]);
            }

            return $application;
        });
    }

    /**
     * Update application status with audit history and notification.
     */
    public function updateStatus(Application $application, string $newStatus, User $changedByUser, ?string $remarks = null): Application
    {
        return DB::transaction(function () use ($application, $newStatus, $changedByUser, $remarks) {
            $oldStatus = $application->status;
            $application->update(['status' => $newStatus]);

            ApplicationStatusHistory::create([
                'application_id' => $application->id,
                'status' => $newStatus,
                'changed_at' => now(),
                'changed_by' => $changedByUser->id,
                'remarks' => $remarks ?? "Status updated from '{$oldStatus}' to '{$newStatus}'.",
            ]);

            // Notify student
            if ($application->student && $application->student->user_id) {
                Notification::create([
                    'user_id' => $application->student->user_id,
                    'type' => 'application_update',
                    'title' => 'Application Status Update',
                    'message' => "Your application status for '{$application->job->title}' is now: {$newStatus}.",
                    'data' => [
                        'application_id' => $application->id,
                        'job_id' => $application->job_id,
                        'new_status' => $newStatus,
                    ],
                ]);
            }

            return $application;
        });
    }
}
