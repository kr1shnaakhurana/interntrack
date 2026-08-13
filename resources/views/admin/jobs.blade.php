@extends('layouts.dashboard')

@section('title', 'All Jobs - InternTrack Admin')
@section('page_title', 'All Job Listings')

@section('content')
<div class="card card-custom bg-white p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Posted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>
                            <a href="/jobs/{{ $job->id }}" class="fw-bold text-dark text-decoration-none">{{ $job->title }}</a>
                            @if($job->location)
                                <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $job->location }}</div>
                            @endif
                        </td>
                        <td class="fw-semibold text-secondary">{{ $job->company->name ?? 'External' }}</td>
                        <td><x-status-badge :type="$job->job_type" /></td>
                        <td>
                            @if($job->source)
                                <span class="badge {{ $job->source->type === 'direct' ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' }} border">
                                    {{ $job->source->name }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $job->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $job->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            {{ $job->posted_at ? \Carbon\Carbon::parse($job->posted_at)->format('M d, Y') : $job->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">No job listings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$jobs" />
</div>
@endsection
