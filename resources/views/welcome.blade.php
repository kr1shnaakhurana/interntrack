@extends('layouts.app')

@section('title', 'InternTrack - Discover Jobs & Track Applications')

@section('content')
<!-- Hero Section -->
<div class="hero-banner text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-extrabold mb-3">Discover Opportunities. Track Every Application.</h1>
        <p class="lead mb-4 max-w-2xl mx-auto opacity-90">
            InternTrack brings internships, full-time jobs, and research fellowships into a unified platform. Track your entire career journey from application to final offer.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="/jobs" class="btn btn-light btn-lg px-4 fw-bold text-primary shadow">
                <i class="bi bi-search me-2"></i> Find Opportunities
            </a>
            @guest
                <a href="/register" class="btn btn-outline-light btn-lg px-4 fw-semibold">
                    Create Free Account
                </a>
            @endguest
        </div>
    </div>
</div>

<div class="container mb-5">
    <!-- Value Pillars -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100 text-center">
                <div class="display-6 text-primary mb-3"><i class="bi bi-compass"></i></div>
                <h5 class="fw-bold">Multi-Source Discovery</h5>
                <p class="text-muted small mb-0">Browse direct employer postings alongside aggregated external opportunities.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100 text-center">
                <div class="display-6 text-success mb-3"><i class="bi bi-shield-check"></i></div>
                <h5 class="fw-bold">Verified Employers</h5>
                <p class="text-muted small mb-0">Transparent verification badges ensure legitimate organization opportunities.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100 text-center">
                <div class="display-6 text-warning mb-3"><i class="bi bi-kanban"></i></div>
                <h5 class="fw-bold">Application Tracker</h5>
                <p class="text-muted small mb-0">Visual timelines tracking your status from applied to offer selection.</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom p-4 h-100 text-center">
                <div class="display-6 text-purple mb-3"><i class="bi bi-calendar-check"></i></div>
                <h5 class="fw-bold">Interview Rounds</h5>
                <p class="text-muted small mb-0">Manage technical, HR, and online interview rounds seamlessly.</p>
            </div>
        </div>
    </div>

    <!-- Recent Opportunities Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0">Recent Opportunities</h3>
            <p class="text-muted small mb-0">Explore fresh internship and job listings</p>
        </div>
        <a href="/jobs" class="btn btn-outline-primary fw-semibold">View All Jobs <i class="bi bi-arrow-right ms-1"></i></a>
    </div>

    <div class="row g-4">
        @forelse($recentJobs as $job)
            <div class="col-md-4">
                <x-job-card :job="$job" />
            </div>
        @empty
            <div class="col-12">
                <x-empty-state title="No Jobs Listed Yet" message="Run the database seeder or check back later." />
            </div>
        @endforelse
    </div>
</div>
@endsection
