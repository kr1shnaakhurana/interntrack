@extends('layouts.dashboard')

@section('title', 'Student Dashboard - InternTrack')
@section('page_title', 'Student Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="card card-stat bg-primary text-white p-4 mb-4" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }} 👋</h3>
            <p class="mb-0 opacity-90">Track your application pipeline, interview schedules, and saved opportunities.</p>
        </div>
        <a href="/jobs" class="btn btn-light fw-bold text-primary shadow-sm px-4">Find New Jobs</a>
    </div>
</div>

<!-- Metric Cards -->
<div class="row g-3 mb-5">
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white border-start border-primary border-4">
            <div class="text-muted small fw-semibold">TOTAL APPLICATIONS</div>
            <div class="display-6 fw-bold text-dark mt-1">{{ $totalApplications }}</div>
            <a href="/applications" class="small text-primary text-decoration-none mt-2 d-inline-block">View All Applications &rarr;</a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white border-start border-warning border-4">
            <div class="text-muted small fw-semibold">IN PROGRESS</div>
            <div class="display-6 fw-bold text-dark mt-1">{{ $inProgressApplications }}</div>
            <span class="small text-muted mt-2 d-inline-block">Active review status</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white border-start border-success border-4">
            <div class="text-muted small fw-semibold">UPCOMING INTERVIEWS</div>
            <div class="display-6 fw-bold text-dark mt-1">{{ $upcomingInterviewsCount }}</div>
            <a href="/interviews" class="small text-success text-decoration-none mt-2 d-inline-block">View Interview Rounds &rarr;</a>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-stat p-3 bg-white border-start border-info border-4">
            <div class="text-muted small fw-semibold">SAVED JOBS</div>
            <div class="display-6 fw-bold text-dark mt-1">{{ $savedJobsCount }}</div>
            <a href="/saved-jobs" class="small text-info text-decoration-none mt-2 d-inline-block">View Saved Items &rarr;</a>
        </div>
    </div>
</div>

<!-- Recent Opportunities Section -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold m-0">Recent Opportunities</h5>
    <a href="/jobs" class="btn btn-sm btn-outline-primary fw-semibold">Browse All</a>
</div>

<div class="row g-4">
    @forelse($recentJobs as $job)
        <div class="col-md-4">
            <x-job-card :job="$job" />
        </div>
    @empty
        <div class="col-12">
            <x-empty-state title="No Recent Opportunities" message="Check back later for new job postings." />
        </div>
    @endforelse
</div>
@endsection
