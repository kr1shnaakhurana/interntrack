@extends('layouts.dashboard')

@section('title', 'Manage Job Postings - InternTrack')
@section('page_title', 'Job Postings Management')

@section('content')
<div class="d-flex justify-content-end mb-3">
    @if($company->isVerified())
        <a href="/company/jobs/create" class="btn btn-primary fw-semibold">
            <i class="bi bi-plus-circle me-1"></i> Post New Opportunity
        </a>
    @else
        <a href="/company/verification" class="btn btn-outline-warning fw-semibold">
            <i class="bi bi-shield-lock me-1"></i> Verify to Unlock Posting
        </a>
    @endif
</div>

<div class="card card-custom bg-white p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Job Title</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Deadline</th>
                    <th>Applications</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>
                            <a href="/jobs/{{ $job->id }}" class="fw-bold text-dark text-decoration-none">{{ $job->title }}</a>
                        </td>
                        <td><x-status-badge :type="$job->job_type" /></td>
                        <td class="small text-muted">{{ $job->location ?? 'N/A' }} {{ $job->is_remote ? '(Remote)' : '' }}</td>
                        <td class="small text-muted">
                            {{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('M d, Y') : '—' }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $job->applications->count() }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $job->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $job->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="/company/jobs/{{ $job->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <x-empty-state title="No Job Postings" message="Create your first job opportunity to attract candidates." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$jobs" />
</div>
@endsection
