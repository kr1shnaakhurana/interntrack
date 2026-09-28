@extends('layouts.dashboard')

@section('title', 'Company Dashboard - InternTrack')

@section('content')
<div class="company-portal-page">
    @if(!$company->isVerified())
        <div class="alert alert-warning alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-exclamation fs-4 text-warning me-3"></i>
                <div>
                    <strong>Verification Required</strong> — Your company profile is not yet verified.
                    Verified companies can publish direct job opportunities.
                    <a href="/company/verification" class="fw-bold text-warning ms-1">Submit Verification Request &rarr;</a>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @else
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-patch-check-fill fs-4 text-success me-3"></i>
                <div><strong>Verified Organization</strong> — Your company profile is verified. You can publish job opportunities.</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Active Job Postings</span>
                    </div>
                    <div class="display-6 fw-bold text-dark mb-2">{{ $activeJobsCount }}</div>
                    <a href="/company/jobs" class="small text-primary text-decoration-none fw-medium">Manage Jobs &rarr;</a>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Total Job Postings</span>
                    </div>
                    <div class="display-6 fw-bold text-dark mb-2">{{ $totalJobsCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase">Total Applicants</span>
                    </div>
                    <div class="display-6 fw-bold text-dark mb-2">{{ $applicationsCount }}</div>
                    <a href="/company/applications" class="small text-success text-decoration-none fw-medium">View Applicants &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <h5 class="fw-bold text-dark mb-0">Recent Job Postings</h5>
        <div class="d-flex gap-2">
            <a href="/company/jobs" class="btn btn-outline-secondary btn-sm rounded-pill px-4">All Postings</a>
            @if($company->isVerified())
                <a href="/company/jobs/create" class="btn btn-primary btn-sm rounded-pill px-4">Post New Job</a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Job Title</th>
                        <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Type</th>
                        <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Applications</th>
                        <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Status</th>
                        <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Posted</th>
                        <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentJobs as $job)
                        <tr>
                            <td class="px-4 py-4 fw-semibold text-dark">{{ $job->title }}</td>
                            <td class="px-4 py-4"><x-status-badge :type="$job->job_type" /></td>
                            <td class="px-4 py-4 small text-primary">{{ $job->applications->count() }} applicants</td>
                            <td class="px-4 py-4">
                                <span class="badge {{ $job->is_active ? 'bg-primary-light text-primary' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill px-3 py-2">
                                    {{ $job->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 small text-muted">{{ $job->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-4">
                                <a href="/company/jobs/{{ $job->id }}/edit" class="btn btn-outline-primary btn-sm rounded-pill px-4">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No job postings yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .company-portal-page .page-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .company-portal-page .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    /* KPI Cards - no icons, equal height, responsive */
    .company-portal-page .stat-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        height: 100%;
    }
    .company-portal-page .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .company-portal-page .stat-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .company-portal-page .stat-card .display-6 {
        font-size: 2.5rem;
        line-height: 1.2;
    }

    /* Responsive KPI grid - 3 cards per row on desktop, stack on mobile */
    @media (min-width: 992px) {
        .company-portal-page .row.g-3 > .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
        }
    }
    @media (max-width: 991.98px) {
        .company-portal-page .row.g-3 > .col-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    @media (max-width: 575.98px) {
        .company-portal-page .row.g-3 > .col-6,
        .company-portal-page .row.g-3 > .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    /* Recent Job Postings Table */
    .company-portal-page .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .company-portal-page .table {
        min-width: 600px;
        table-layout: auto;
    }
    .company-portal-page .table th,
    .company-portal-page .table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .company-portal-page .table td:first-child {
        max-width: 300px;
    }
    .company-portal-page .table td:last-child {
        white-space: nowrap;
    }

    /* KPI cards equal height */
    .company-portal-page .row.g-3 > [class*="col-"] {
        display: flex;
    }
    .company-portal-page .stat-card {
        width: 100%;
    }
</style>
@endpush