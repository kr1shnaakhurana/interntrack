@extends('layouts.dashboard')

@section('title', 'Company Dashboard - InternTrack')
@section('page_title', 'Company Portal')

@section('content')
<!-- Verification Status Banner -->
@if(!$company->isVerified())
    <div class="alert alert-warning d-flex align-items-center gap-3 mb-4 p-3" role="alert">
        <i class="bi bi-shield-exclamation fs-4"></i>
        <div>
            <strong>Verification Required</strong> — Your company profile is not yet verified.
            Verified companies can publish direct job opportunities.
            <a href="/company/verification" class="fw-bold text-warning ms-1">Submit Verification Request &rarr;</a>
        </div>
    </div>
@else
    <div class="alert alert-success d-flex align-items-center gap-3 mb-4 p-3 border-0" role="alert">
        <i class="bi bi-patch-check-fill fs-4"></i>
        <div><strong>Verified Organization</strong> — Your company profile is verified. You can publish job opportunities.</div>
    </div>
@endif

<!-- Stats Cards -->
<div class="row g-3 mb-5">
    <div class="col-md-4">
        <div class="card card-stat p-3 bg-white border-start border-primary border-4">
            <div class="text-muted small fw-semibold">ACTIVE JOB POSTINGS</div>
            <div class="display-6 fw-bold text-dark mt-1">{{ $activeJobsCount }}</div>
            <a href="/company/jobs" class="small text-primary text-decoration-none mt-2 d-inline-block">Manage Jobs &rarr;</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat p-3 bg-white border-start border-secondary border-4">
            <div class="text-muted small fw-semibold">TOTAL JOB POSTINGS</div>
            <div class="display-6 fw-bold text-dark mt-1">{{ $totalJobsCount }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-stat p-3 bg-white border-start border-success border-4">
            <div class="text-muted small fw-semibold">TOTAL APPLICANTS</div>
            <div class="display-6 fw-bold text-dark mt-1">{{ $applicationsCount }}</div>
            <a href="/company/applications" class="small text-success text-decoration-none mt-2 d-inline-block">View Applicants &rarr;</a>
        </div>
    </div>
</div>

<!-- Recent Job Listings -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold m-0">Recent Job Postings</h5>
    <div class="d-flex gap-2">
        <a href="/company/jobs" class="btn btn-sm btn-outline-secondary">All Postings</a>
        @if($company->isVerified())
            <a href="/company/jobs/create" class="btn btn-sm btn-primary fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Post New Job
            </a>
        @endif
    </div>
</div>

<div class="card card-custom bg-white p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Job Title</th>
                    <th>Type</th>
                    <th>Applications</th>
                    <th>Status</th>
                    <th>Posted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentJobs as $job)
                    <tr>
                        <td class="fw-semibold">{{ $job->title }}</td>
                        <td><x-status-badge :type="$job->job_type" /></td>
                        <td>{{ $job->applications->count() }} applicants</td>
                        <td>
                            <span class="badge {{ $job->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $job->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $job->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="/company/jobs/{{ $job->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">No job postings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
