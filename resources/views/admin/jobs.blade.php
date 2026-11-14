@extends('layouts.dashboard')

@section('title', 'All Jobs - InternTrack Admin')
@section('page_title', 'All Platform Job Listings')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <div>
        <p class="text-muted small mb-0">System-wide monitoring of all direct employer postings and external aggregated job feeds.</p>
    </div>
    <div class="text-muted small">
        Showing <strong class="text-dark">{{ $jobs->total() }}</strong> total postings
    </div>
</div>

<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Employer</th>
                    <th>Type</th>
                    <th>Source Feed</th>
                    <th>Status</th>
                    <th class="text-end">Posted Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>
                            <a href="/jobs/{{ $job->id }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $job->title }}
                            </a>
                            @if($job->location)
                                <div class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $job->location }}</div>
                            @endif
                        </td>
                        <td class="text-secondary small">{{ $job->company->name ?? 'External' }}</td>
                        <td><x-status-badge :type="$job->job_type" /></td>
                        <td>
                            @if($job->source)
                                <span class="badge {{ $job->source->type === 'direct' ? 'badge-subtle-primary' : 'badge-subtle-neutral' }}">
                                    {{ $job->source->name }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <x-status-badge :status="$job->is_active ? 'active' : 'inactive'" />
                        </td>
                        <td class="text-muted small text-end">
                            {{ $job->posted_at ? \Carbon\Carbon::parse($job->posted_at)->format('M d, Y') : $job->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <x-empty-state title="No Job Listings Found" message="There are currently no job postings recorded in the database." icon="bi-briefcase" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$jobs" />
@endsection
