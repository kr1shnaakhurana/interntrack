<?php

use App\Http\Controllers\Web\CompanyPortalController;
use App\Http\Controllers\Web\LandingController;
use App\Http\Controllers\Web\StudentDashboardController;
use App\Http\Controllers\Web\WebAdminController;
use App\Http\Controllers\Web\WebApplicationController;
use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\WebInterviewController;
use App\Http\Controllers\Web\WebJobController;
use App\Http\Controllers\Web\WebProfileController;
use App\Http\Controllers\Web\WebSavedJobController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - InternTrack Blade Frontend
|--------------------------------------------------------------------------
*/

// ─── PUBLIC ROUTES ───────────────────────────────────────────────────────────

Route::get('/', [LandingController::class, 'index'])->name('home');

// Job Discovery (public browsing)
Route::get('/jobs', [WebJobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{id}', [WebJobController::class, 'show'])->name('jobs.show');

// ─── AUTH ROUTES ─────────────────────────────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [WebAuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── STUDENT ROUTES ──────────────────────────────────────────────────────────

Route::middleware(['auth'])->group(function () {
    // Student Dashboard
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Job interactions (auth required for saving/applying)
    Route::post('/jobs/{id}/save', [WebJobController::class, 'saveJob'])->name('jobs.save');
    Route::post('/jobs/{id}/apply', [WebJobController::class, 'applyJob'])->name('jobs.apply');

    // Application Tracker
    Route::get('/applications', [WebApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{id}', [WebApplicationController::class, 'show'])->name('applications.show');

    // Interview Schedule
    Route::get('/interviews', [WebInterviewController::class, 'index'])->name('interviews.index');

    // Saved Jobs Bookmarks
    Route::get('/saved-jobs', [WebSavedJobController::class, 'index'])->name('saved-jobs.index');
    Route::delete('/saved-jobs/{id}', [WebSavedJobController::class, 'destroy'])->name('saved-jobs.destroy');

    // Student Profile & Resume Vault
    Route::get('/profile', [WebProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [WebProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/resume', [WebProfileController::class, 'uploadResume'])->name('profile.resume.upload');
});

// ─── COMPANY PORTAL ROUTES ───────────────────────────────────────────────────

Route::middleware(['auth'])->prefix('company')->name('company.')->group(function () {
    Route::get('/dashboard', [CompanyPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/verification', [CompanyPortalController::class, 'verification'])->name('verification');
    Route::post('/verification', [CompanyPortalController::class, 'submitVerification'])->name('verification.submit');
    Route::get('/jobs', [CompanyPortalController::class, 'jobs'])->name('jobs.index');
    Route::get('/jobs/create', [CompanyPortalController::class, 'createJob'])->name('jobs.create');
    Route::post('/jobs', [CompanyPortalController::class, 'storeJob'])->name('jobs.store');
    Route::get('/jobs/{id}/edit', [CompanyPortalController::class, 'editJob'])->name('jobs.edit');
    Route::put('/jobs/{id}', [CompanyPortalController::class, 'updateJob'])->name('jobs.update');
    Route::get('/applications', [CompanyPortalController::class, 'applications'])->name('applications');
});

// ─── ADMIN CONSOLE ROUTES ────────────────────────────────────────────────────

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [WebAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/verifications', [WebAdminController::class, 'verifications'])->name('verifications');
    Route::post('/verifications/{id}/approve', [WebAdminController::class, 'approveVerification'])->name('verifications.approve');
    Route::post('/verifications/{id}/reject', [WebAdminController::class, 'rejectVerification'])->name('verifications.reject');
    Route::get('/companies', [WebAdminController::class, 'companies'])->name('companies');
    Route::get('/jobs', [WebAdminController::class, 'jobs'])->name('jobs');
    Route::get('/users', [WebAdminController::class, 'users'])->name('users');
    Route::get('/logs', [WebAdminController::class, 'logs'])->name('logs');
    Route::post('/aggregator', [WebAdminController::class, 'runAggregator'])->name('aggregator');
});

// ─── DEV TOOLS (kept for development convenience) ────────────────────────────

Route::get('/dev-ui', function () {
    return view('dev-ui');
});

Route::post('/dev-ui/reseed', function () {
    Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
    return response()->json([
        'success' => true,
        'message' => 'Database successfully reset and seeded with fresh demo data.',
    ]);
});
