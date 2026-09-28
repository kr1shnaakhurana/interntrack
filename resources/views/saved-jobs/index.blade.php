@extends('layouts.app')

@section('title', 'Saved Jobs - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <h1 class="page-title">Saved Jobs</h1>
    <a href="/jobs" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus-circle me-1"></i> Explore Jobs</a>
</div>

<div class="row g-4">
    @forelse($savedJobs as $saved)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">
                                <a href="/jobs/{{ $saved->job->id }}" class="text-decoration-none text-dark">{{ $saved->job->title }}</a>
                            </h5>
                            <div class="fw-medium text-secondary small">{{ $saved->job->company->name ?? 'Company' }}</div>
                        </div>
                        <div class="flex-shrink-0"><x-status-badge :type="$saved->job->job_type" /></div>
                    </div>

                    <div class="flex-grow-1">
                        <div class="text-muted small mb-2 d-flex align-items-center flex-wrap gap-2">
                            <span><i class="bi bi-geo-alt me-1"></i> {{ $saved->job->location ?? 'N/A' }}</span>
                            @if($saved->job->is_remote)
                                <span class="badge bg-success-light text-success rounded-pill px-2 py-1">Remote</span>
                            @endif
                        </div>

                        @if($saved->job->deadline)
                            <div class="text-muted small mb-2">
                                <i class="bi bi-clock-history me-1"></i> Deadline: {{ \Carbon\Carbon::parse($saved->job->deadline)->format('M d, Y') }}
                            </div>
                        @endif

                        <div class="small text-muted mb-4">
                            Saved on {{ \Carbon\Carbon::parse($saved->created_at)->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-auto pt-3 border-top">
                        <a href="/jobs/{{ $saved->job->id }}" class="btn btn-primary btn-sm rounded-pill px-4 flex-fill">View & Apply</a>
                        <form action="/saved-jobs/{{ $saved->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Remove Bookmark">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <x-empty-state title="No Saved Jobs" message="Browse opportunities and bookmark the ones you find interesting." icon="bi-bookmark-heart" />
            <div class="text-center mt-4">
                <a href="/jobs" class="btn btn-primary rounded-pill px-4">Explore Opportunities</a>
            </div>
        </div>
    @endforelse
</div>

<x-pagination :paginator="$savedJobs" />
@endsection