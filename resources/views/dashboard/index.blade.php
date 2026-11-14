@extends('layouts.app')

@section('title', 'Student Dashboard - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Candidate Dashboard</h1>
        <p class="text-muted small mb-0">Overview of your ongoing applications, interview pipeline, and bookmarked opportunities.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="/profile" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-person"></i> Profile &amp; Resumes
        </a>
        <a href="/jobs" class="btn btn-primary btn-sm">
            <i class="bi bi-briefcase"></i> Browse Opportunities
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Applications Submitted</div>
                <div class="stat-metric-value">{{ $totalApplications }}</div>
            </div>
            <a href="/applications" class="stat-metric-link">View all applications &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">In Active Review</div>
                <div class="stat-metric-value">{{ $inProgressApplications }}</div>
            </div>
            <a href="/applications?status=in_progress" class="stat-metric-link">Filter in-progress &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Upcoming Interviews</div>
                <div class="stat-metric-value">{{ $upcomingInterviewsCount }}</div>
            </div>
            <a href="/interviews" class="stat-metric-link">View schedule &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Saved Opportunities</div>
                <div class="stat-metric-value">{{ $savedJobsCount }}</div>
            </div>
            <a href="/saved-jobs" class="stat-metric-link">View bookmarked &rarr;</a>
        </div>
    </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="section-title mb-0">Recent Opportunities</h2>
        <p class="text-muted small mb-0">Latest job and internship postings from verified employers and partner feeds.</p>
    </div>
    <a href="/jobs" class="btn btn-outline-secondary btn-sm">Browse All Roles</a>
</div>

<div class="row g-3">
    @forelse($recentJobs as $job)
        <div class="col-12 col-md-6 col-lg-4">
            <x-job-card :job="$job" />
        </div>
    @empty
        <div class="col-12">
            <div class="card p-4">
                <x-empty-state title="No Recent Opportunities" message="There are currently no new job listings. Check back shortly." icon="bi-briefcase" />
            </div>
        </div>
    @endforelse
</div>
@endsection