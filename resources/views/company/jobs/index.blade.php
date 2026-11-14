@extends('layouts.dashboard')

@section('title', 'Manage Job Postings - InternTrack')
@section('page_title', 'Job Postings Management')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <div>
        <p class="text-muted small mb-0">Create, edit, and monitor application volume for your organization's open roles.</p>
    </div>
    <div class="d-flex gap-2">
        @if($company->isVerified())
            <a href="/company/jobs/create" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Post New Opportunity
            </a>
        @else
            <a href="/company/verification" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-shield-lock"></i> Verify to Unlock Posting
            </a>
        @endif
    </div>
</div>

<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Deadline</th>
                    <th>Candidates</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>
                            <a href="/jobs/{{ $job->id }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $job->title }}
                            </a>
                        </td>
                        <td><x-status-badge :type="$job->job_type" /></td>
                        <td class="small text-muted">
                            {{ $job->location ?? 'Not Specified' }}
                            @if($job->is_remote)
                                <span class="badge badge-subtle-success ms-1">Remote</span>
                            @endif
                        </td>
                        <td class="small text-muted">
                            {{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('M d, Y') : 'Open until filled' }}
                        </td>
                        <td>
                            <span class="badge badge-subtle-primary">
                                {{ $job->applications->count() }} applicants
                            </span>
                        </td>
                        <td>
                            <x-status-badge :status="$job->is_active ? 'active' : 'inactive'" />
                        </td>
                        <td class="text-end">
                            <a href="/company/jobs/{{ $job->id }}/edit" class="btn btn-outline-secondary btn-sm">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <x-empty-state title="No Job Postings" message="Your organization has not created any job or internship postings yet." icon="bi-briefcase">
                                @if($company->isVerified())
                                    <div class="mt-3">
                                        <a href="/company/jobs/create" class="btn btn-primary btn-sm">Post New Job</a>
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

<x-pagination :paginator="$jobs" />
@endsection