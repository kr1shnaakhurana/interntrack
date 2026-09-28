@extends('layouts.app')

@section('title', 'Student Dashboard - InternTrack')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>

<div class="card greeting-banner border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3 min-w-0">
                @php
                    $initials = collect(explode(' ', trim(auth()->user()->name)))->filter()->take(2)->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                @endphp
                <div class="greeting-avatar rounded-circle flex-shrink-0">
                    {{ $initials ?: 'ST' }}
                </div>
                <div class="text-truncate">
                    <h2 class="h5 fw-bold text-white mb-1 text-truncate">Welcome back, {{ auth()->user()->name }}</h2>
                    <p class="greeting-subtitle mb-0 text-white text-truncate">Track your applications, interviews, and saved opportunities.</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <a href="/profile" class="btn-ghost-light btn btn-sm fw-medium rounded-pill px-4 py-2">Complete Profile</a>
                <a href="/jobs" class="btn btn-light btn-sm fw-medium rounded-pill px-4 py-2">Find New Jobs</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Applications</span>
                    <div class="bg-primary-light text-primary rounded-3 p-2">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $totalApplications }}</div>
                <a href="/applications" class="small text-primary text-decoration-none fw-medium">View All Applications &rarr;</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">In Progress</span>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $inProgressApplications }}</div>
                <a href="/applications?status=in_progress" class="small text-dark-yellow text-decoration-none fw-medium">Active review status &rarr;</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Upcoming Interviews</span>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                        <i class="bi bi-calendar-check fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $upcomingInterviewsCount }}</div>
                <a href="/interviews" class="small text-success text-decoration-none fw-medium">View Interview Rounds &rarr;</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Saved Jobs</span>
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                        <i class="bi bi-bookmark fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $savedJobsCount }}</div>
                <a href="/saved-jobs" class="small text-dark-blue text-decoration-none fw-medium">View Saved Items &rarr;</a>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <h5 class="fw-bold text-dark mb-0">Recent Opportunities</h5>
    <div>
        <a href="/jobs" class="btn btn-outline-primary btn-sm rounded-pill px-4">Browse All</a>
    </div>
</div>

<div class="row g-4">
    @forelse($recentJobs as $job)
        <div class="col-12 col-md-6 col-lg-4">
            <x-job-card :job="$job" />
        </div>
    @empty
        <div class="col-12">
            <x-empty-state title="No Recent Opportunities" message="Check back later for new job postings." icon="bi-briefcase" />
        </div>
    @endforelse
</div>
@endsection

@push('styles')
<style>
    .greeting-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: var(--radius-lg);
    }
    .greeting-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        font-size: 1.05rem;
        font-weight: 700;
        color: #fff;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.25);
    }
    .greeting-subtitle {
        opacity: 0.75;
        font-size: 0.875rem;
    }
    .btn-ghost-light {
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .text-dark-yellow { color: #b45309 !important; }
    .text-dark-yellow:hover { color: #92400e !important; }
    .text-dark-blue { color: #0369a1 !important; }
    .text-dark-blue:hover { color: #024d75 !important; }
</style>
@endpush