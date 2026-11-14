@extends('layouts.app')

@section('title', 'Saved Opportunities - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Saved Opportunities</h1>
        <p class="text-muted small mb-0">Bookmarked internships and career opportunities saved for review.</p>
    </div>
    <a href="/jobs" class="btn btn-primary btn-sm">
        <i class="bi bi-briefcase"></i> Explore Directory
    </a>
</div>

<div class="row g-3">
    @forelse($savedJobs as $saved)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-body p-3 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div class="flex-grow-1 min-w-0">
                            <h5 class="fs-6 fw-semibold mb-1 text-truncate">
                                <a href="/jobs/{{ $saved->job->id }}" class="text-dark text-decoration-none">{{ $saved->job->title }}</a>
                            </h5>
                            <span class="text-secondary small fw-medium">{{ $saved->job->company->name ?? 'Company' }}</span>
                        </div>
                        <div class="flex-shrink-0">
                            <x-status-badge :type="$saved->job->job_type" />
                        </div>
                    </div>

                    <div class="flex-grow-1 d-flex flex-column gap-1 my-2">
                        <div class="text-muted small d-flex align-items-center flex-wrap gap-2">
                            <span><i class="bi bi-geo-alt me-1"></i>{{ $saved->job->location ?? 'Not Specified' }}</span>
                            @if($saved->job->is_remote)
                                <span class="badge badge-subtle-success">Remote</span>
                            @endif
                        </div>

                        @if($saved->job->deadline)
                            <div class="text-muted small">
                                <i class="bi bi-calendar3 me-1"></i> Deadline: {{ \Carbon\Carbon::parse($saved->job->deadline)->format('M d, Y') }}
                            </div>
                        @endif

                        <div class="text-muted small mt-1" style="font-size: 11.5px;">
                            Saved on {{ \Carbon\Carbon::parse($saved->created_at)->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="pt-2 mt-auto border-top d-flex align-items-center gap-2">
                        <a href="/jobs/{{ $saved->job->id }}" class="btn btn-primary btn-sm flex-grow-1">View &amp; Apply</a>
                        <form action="/saved-jobs/{{ $saved->id }}" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove Bookmark">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-4">
                <x-empty-state title="No Bookmarked Opportunities" message="You have not saved any jobs yet. Bookmark roles while exploring the directory." icon="bi-bookmark">
                    <div class="mt-3">
                        <a href="/jobs" class="btn btn-primary btn-sm">Explore Opportunities</a>
                    </div>
                </x-empty-state>
            </div>
        </div>
    @endforelse
</div>
@endsection