@extends('layouts.dashboard')

@section('title', 'Company Dashboard - InternTrack')
@section('page_title', 'Employer Overview')

@section('content')
<div class="company-portal-page">
    @if(!$company->isVerified())
        <div class="alert alert-warning mb-4" role="alert">
            <i class="bi bi-shield-exclamation fs-6 text-warning mt-0.5"></i>
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center flex-grow-1 gap-2">
                <div>
                    <strong>Verification Pending</strong> &mdash; Your employer profile requires verification before you can publish direct opportunities.
                </div>
                <a href="/company/verification" class="btn btn-outline-secondary btn-sm text-nowrap">
                    Submit Verification &rarr;
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-success mb-4" role="alert">
            <i class="bi bi-patch-check-fill fs-6 text-success mt-0.5"></i>
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center flex-grow-1 gap-2">
                <div>
                    <strong>Verified Employer</strong> &mdash; Your organization is approved for publishing direct opportunities and reviewing applicants.
                </div>
                <a href="/company/jobs/create" class="btn btn-primary btn-sm text-nowrap">
                    <i class="bi bi-plus-circle"></i> Post Opportunity
                </a>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-metric-card h-100">
                <div>
                    <div class="stat-metric-label">Active Job Postings</div>
                    <div class="stat-metric-value">{{ $activeJobsCount }}</div>
                </div>
                <a href="/company/jobs" class="stat-metric-link">Manage postings &rarr;</a>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-metric-card h-100">
                <div>
                    <div class="stat-metric-label">Total Listings</div>
                    <div class="stat-metric-value">{{ $totalJobsCount }}</div>
                </div>
                <span class="text-muted small">All active &amp; archived</span>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="stat-metric-card h-100">
                <div>
                    <div class="stat-metric-label">Total Applicants</div>
                    <div class="stat-metric-value">{{ $applicationsCount }}</div>
                </div>
                <a href="/company/applications" class="stat-metric-link">Review candidates &rarr;</a>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
        <div>
            <h2 class="section-title mb-0">Recent Job Postings</h2>
            <p class="text-muted small mb-0">Active and recently updated recruitment listings from your organization.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="/company/jobs" class="btn btn-outline-secondary btn-sm">All Postings</a>
            @if($company->isVerified())
                <a href="/company/jobs/create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Post New Job
                </a>
            @endif
        </div>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Type</th>
                        <th>Applications</th>
                        <th>Status</th>
                        <th>Date Posted</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentJobs as $job)
                        <tr>
                            <td>
                                <a href="/jobs/{{ $job->id }}" class="fw-semibold text-dark text-decoration-none">
                                    {{ $job->title }}
                                </a>
                            </td>
                            <td><x-status-badge :type="$job->job_type" /></td>
                            <td>
                                <span class="badge badge-subtle-primary">
                                    {{ $job->applications->count() }} candidates
                                </span>
                            </td>
                            <td>
                                <x-status-badge :status="$job->is_active ? 'active' : 'inactive'" />
                            </td>
                            <td class="small text-muted">{{ $job->created_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a href="/company/jobs/{{ $job->id }}/edit" class="btn btn-outline-secondary btn-sm">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <x-empty-state title="No Job Postings" message="Your organization has not published any openings yet." icon="bi-briefcase">
                                    @if($company->isVerified())
                                        <div class="mt-3">
                                            <a href="/company/jobs/create" class="btn btn-primary btn-sm">Post Your First Opportunity</a>
                                        </div>
                                    @endif
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection