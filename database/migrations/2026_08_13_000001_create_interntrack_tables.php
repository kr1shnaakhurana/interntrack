<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('student'); // student, company, admin
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Students Table
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('roll_no')->unique()->nullable();
            $table->string('program')->nullable();
            $table->string('department')->nullable();
            $table->string('year')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->string('profile_image_url')->nullable();
            $table->timestamps();
        });

        // 3. Companies Table
        Schema::create('companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('website')->nullable();
            $table->string('industry')->nullable();
            $table->string('size')->nullable();
            $table->string('location')->nullable();
            $table->text('about')->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('is_claimed')->default(false);
            $table->string('sector')->default('private'); // private, government, ngo, other
            $table->boolean('is_external')->default(false);
            $table->timestamps();
        });

        // 4. Company Verifications Table
        Schema::create('company_verifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('documents_url')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->boolean('is_latest')->default(true);
            $table->timestamps();
        });

        // 5. Admins Table
        Schema::create('admins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        // 6. Admin Logs Table
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->string('action');
            $table->string('entity_type');
            $table->uuid('entity_id');
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 7. Job Sources Table
        Schema::create('job_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type')->default('external'); // external, direct
            $table->string('base_url')->nullable();
            $table->string('api_endpoint')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 8. Jobs Table
        Schema::create('jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUuid('source_id')->nullable()->constrained('job_sources')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('job_type')->default('internship'); // internship, full-time, part-time, research, fellowship, government, other
            $table->string('location')->nullable();
            $table->boolean('is_remote')->default(false);
            $table->string('stipend_salary')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->string('experience_required')->nullable();
            $table->text('skills_required')->nullable();
            $table->string('job_url')->nullable();
            $table->string('source_job_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
        });

        // 9. Skills Table
        Schema::create('skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('category')->nullable();
            $table->timestamps();
        });

        // 10. Student Skills Table
        Schema::create('student_skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->string('proficiency')->default('intermediate'); // beginner, intermediate, advanced
            $table->timestamp('created_at')->useCurrent();
        });

        // 11. Job Skills Table
        Schema::create('job_skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->foreignUuid('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->string('importance')->default('medium'); // low, medium, high
            $table->timestamp('created_at')->useCurrent();
        });

        // 12. Resumes Table
        Schema::create('resumes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_url');
            $table->boolean('is_default')->default(false);
            $table->integer('version')->default(1);
            $table->timestamps();
        });

        // 13. Saved Jobs Table
        Schema::create('saved_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->timestamp('saved_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'job_id']);
        });

        // 14. Applications Table
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('job_id')->constrained('jobs')->cascadeOnDelete();
            $table->string('status')->default('applied'); // saved, applied, shortlisted, interview, selected, rejected
            $table->timestamp('applied_at')->nullable();
            $table->string('cover_letter_url')->nullable();
            $table->string('expected_salary')->nullable();
            $table->string('current_ctc')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'job_id']);
        });

        // 15. Application Status History Table
        Schema::create('application_status_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained('applications')->cascadeOnDelete();
            $table->string('status');
            $table->timestamp('changed_at')->useCurrent();
            $table->foreignUuid('changed_by')->constrained('users')->cascadeOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 16. Interviews Table
        Schema::create('interviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->constrained('applications')->cascadeOnDelete();
            $table->integer('round_number')->default(1);
            $table->string('round_name')->nullable();
            $table->string('interview_type')->default('online'); // phone, online, offline, technical, hr
            $table->timestamp('scheduled_at')->nullable();
            $table->string('interviewer')->nullable();
            $table->string('location_meeting_link')->nullable();
            $table->text('notes')->nullable();
            $table->string('result')->default('pending'); // pending, cleared, rejected
            $table->timestamps();
        });

        // 17. Notifications Table
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // application_update, job_match, interview_reminder, system
            $table->string('title');
            $table->text('message');
            $table->jsonb('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('application_status_history');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('saved_jobs');
        Schema::dropIfExists('resumes');
        Schema::dropIfExists('job_skills');
        Schema::dropIfExists('student_skills');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_sources');
        Schema::dropIfExists('admin_logs');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('company_verifications');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('students');
        Schema::dropIfExists('users');
    }
};
