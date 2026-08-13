<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\InterviewController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\SkillController;
use App\Http\Controllers\Api\V1\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| InternTrack API V1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // --- Authentication Routes ---
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login'])->name('login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // --- Public Job Routes ---
    Route::get('jobs', [JobController::class, 'index']);
    Route::get('jobs/{id}', [JobController::class, 'show']);
    Route::get('jobs/{id}/skills', [SkillController::class, 'jobSkills']);

    // --- Public Companies & Skills Routes ---
    Route::get('companies/{id}', [CompanyController::class, 'show']);
    Route::get('companies/{id}/verification', [CompanyController::class, 'getVerifications']);
    Route::get('skills', [SkillController::class, 'index']);

    // --- Authenticated Routes ---
    Route::middleware('auth:sanctum')->group(function () {

        // Student Profile & Activities
        Route::prefix('students/me')->middleware('role:student,admin')->group(function () {
            Route::get('/', [StudentController::class, 'showMe']);
            Route::put('/', [StudentController::class, 'updateMe']);
            Route::get('applications', [StudentController::class, 'myApplications']);
            Route::get('saved-jobs', [StudentController::class, 'mySavedJobs']);
            Route::get('skills', [StudentController::class, 'mySkills']);
            Route::post('skills', [StudentController::class, 'addSkill']);
        });

        // Company Actions
        Route::post('companies', [CompanyController::class, 'store']);
        Route::put('companies/{id}', [CompanyController::class, 'update']);
        Route::post('companies/{id}/verification', [CompanyController::class, 'requestVerification']);

        // Direct Company Job Postings
        Route::prefix('company/jobs')->middleware('role:company,admin')->group(function () {
            Route::get('/', [JobController::class, 'companyJobs']);
            Route::post('/', [JobController::class, 'storeDirectJob']);
            Route::put('{id}', [JobController::class, 'updateDirectJob']);
            Route::delete('{id}', [JobController::class, 'destroyDirectJob']);
        });

        // Application Tracking
        Route::prefix('applications')->group(function () {
            Route::post('/', [ApplicationController::class, 'store'])->middleware('role:student,admin');
            Route::get('/', [ApplicationController::class, 'index']);
            Route::get('{id}', [ApplicationController::class, 'show']);
            Route::post('{id}/status', [ApplicationController::class, 'updateStatus'])->middleware('role:company,admin');

            // Interviews
            Route::get('{id}/interviews', [InterviewController::class, 'index']);
            Route::post('{id}/interviews', [InterviewController::class, 'store'])->middleware('role:company,admin');
        });

        Route::put('interviews/{id}', [InterviewController::class, 'update'])->middleware('role:company,admin');

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);

        // Admin Endpoints
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::get('dashboard', [AdminController::class, 'dashboard']);
            Route::get('company-verifications', [AdminController::class, 'companyVerifications']);
            Route::post('company-verifications/{id}/approve', [AdminController::class, 'approveVerification']);
            Route::post('company-verifications/{id}/reject', [AdminController::class, 'rejectVerification']);
            Route::get('jobs', [AdminController::class, 'jobs']);
            Route::get('companies', [AdminController::class, 'companies']);
            Route::get('users', [AdminController::class, 'users']);
            Route::get('logs', [AdminController::class, 'logs']);
            Route::post('ingest-external-jobs', [AdminController::class, 'triggerExternalIngestion']);
        });
    });
});
