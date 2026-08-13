@extends('layouts.dashboard')

@section('title', 'Saved Jobs - InternTrack')
@section('page_title', 'Saved Job Bookmarks')

@section('content')
<div class="row g-4">
    @forelse($savedJobs as $saved)
        <div class="col-md-4">
            <div class="card card-custom h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <a href="/jobs/{{ $saved->job->id }}" class="text-decoration-none text-dark">{{ $saved->job->title }}</a>
                        </h5>
                        <div class="fw-semibold text-secondary small">{{ $saved->job->company->name ?? 'Company' }}</div>
                    </div>
                    <x-status-badge :type="$saved->job->job_type" />
                </div>

                <div class="text-muted small mb-2">
                    <i class="bi bi-geo-alt me-1"></i> {{ $saved->job->location ?? 'N/A' }}
                    @if($saved->job->is_remote)
                        <span class="badge bg-info-subtle text-info ms-1">Remote</span>
                    @endif
                </div>

                @if($saved->job->deadline)
                    <div class="text-muted small mb-2">
                        <i class="bi bi-clock-history me-1"></i> Deadline: {{ \Carbon\Carbon::parse($saved->job->deadline)->format('M d, Y') }}
                    </div>
                @endif

                <div class="small text-muted mb-3">
                    Saved on {{ \Carbon\Carbon::parse($saved->created_at)->format('M d, Y') }}
                </div>

                <div class="mt-auto pt-3 border-top d-flex gap-2">
                    <a href="/jobs/{{ $saved->job->id }}" class="btn btn-primary btn-sm fw-semibold flex-fill">View & Apply</a>
                    <form action="/saved-jobs/{{ $saved->id }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove Bookmark">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <x-empty-state title="No Saved Jobs" message="Browse opportunities and bookmark the ones you find interesting." />
            <div class="text-center mt-3">
                <a href="/jobs" class="btn btn-primary fw-semibold px-4">Explore Opportunities</a>
            </div>
        </div>
    @endforelse
</div>

<x-pagination :paginator="$savedJobs" />
@endsection
