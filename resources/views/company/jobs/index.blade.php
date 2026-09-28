@extends('layouts.dashboard')

@section('title', 'Manage Job Postings - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <h1 class="page-title">Job Postings Management</h1>
    <div class="d-flex gap-2">
        @if($company->isVerified())
            <a href="/company/jobs/create" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-circle me-1"></i> Post New Opportunity
            </a>
        @else
            <a href="/company/verification" class="btn btn-outline-primary rounded-pill px-4">
                <i class="bi bi-shield-lock me-1"></i> Verify to Unlock Posting
            </a>
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
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Location</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Deadline</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Applications</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Status</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td class="px-4 py-4">
                            <a href="/jobs/{{ $job->id }}" class="fw-bold text-dark text-decoration-none">{{ $job->title }}</a>
                        </td>
                        <td class="px-4 py-4"><x-status-badge :type="$job->job_type" /></td>
                        <td class="px-4 py-4 small text-muted">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span>{{ $job->location ?? 'N/A' }}</span>
                                @if($job->is_remote)
                                    <span class="badge bg-success-light text-success rounded-pill px-2 py-1">Remote</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 small text-muted">
                            {{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-4 py-4">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2 border">{{ $job->applications->count() }}</span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="badge {{ $job->is_active ? 'bg-primary-light text-primary' : 'bg-secondary bg-opacity-10 text-secondary' }} rounded-pill px-3 py-2">
                                {{ $job->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <a href="/company/jobs/{{ $job->id }}/edit" class="btn btn-outline-primary btn-sm rounded-pill px-4">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <x-empty-state title="No Job Postings" message="Create your first job opportunity to attract candidates." icon="bi-briefcase" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jobs->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <x-pagination :paginator="$jobs" />
        </div>
    @endif
</div>
@endsection