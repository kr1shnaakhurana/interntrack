@extends('layouts.app')

@section('title', 'Explore Jobs & Internships - InternTrack')

@section('content')
<div class="container pt-3">
    <div class="row mb-3">
        <div class="col-12">
            <h2 class="fw-bold text-dark mb-1">Opportunity Discovery</h2>
            <p class="text-muted small mb-0">Search through verified direct employer postings and aggregated external feeds.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
        <div class="card-body p-3">
            <form action="/jobs" method="GET">
                <div class="jobs-filter-bar">
                    <label for="keyword" class="visually-hidden">Search</label>
                    <input type="text" name="keyword" id="keyword" class="form-control form-control-sm rounded-3 flex-grow-1" placeholder="Search title, skills, keyword..." value="{{ request('keyword') }}">

                    <label for="job_type" class="visually-hidden">Job Type</label>
                    <select name="job_type" id="job_type" class="form-select form-select-sm rounded-3 w-auto">
                        <option value="">All Job Types</option>
                        <option value="internship" {{ request('job_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                        <option value="full-time" {{ request('job_type') == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                        <option value="part-time" {{ request('job_type') == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                        <option value="research" {{ request('job_type') == 'research' ? 'selected' : '' }}>Research</option>
                        <option value="fellowship" {{ request('job_type') == 'fellowship' ? 'selected' : '' }}>Fellowship</option>
                        <option value="government" {{ request('job_type') == 'government' ? 'selected' : '' }}>Government</option>
                    </select>

                    <label for="location" class="visually-hidden">Location</label>
                    <input type="text" name="location" id="location" class="form-control form-control-sm rounded-3" placeholder="Location..." value="{{ request('location') }}">

                    <label for="sector" class="visually-hidden">Sector</label>
                    <select name="sector" id="sector" class="form-select form-select-sm rounded-3 w-auto">
                        <option value="">All Sectors</option>
                        <option value="private" {{ request('sector') == 'private' ? 'selected' : '' }}>Private</option>
                        <option value="government" {{ request('sector') == 'government' ? 'selected' : '' }}>Government</option>
                        <option value="ngo" {{ request('sector') == 'ngo' ? 'selected' : '' }}>NGO</option>
                    </select>
                </div>

                <div class="jobs-filter-bar jobs-filter-bar--options border-top pt-3 mt-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_remote" value="1" id="is_remote" {{ request('is_remote') ? 'checked' : '' }}>
                        <label class="form-check-label small fw-medium text-dark text-nowrap" for="is_remote">Remote Only</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="verified_only" value="1" id="verified_only" {{ request('verified_only') ? 'checked' : '' }}>
                        <label class="form-check-label small fw-medium text-dark text-nowrap" for="verified_only">Verified Employers Only</label>
                    </div>

                    <div class="d-flex align-items-center gap-2 ms-auto">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                        <a href="/jobs" class="btn btn-outline-secondary btn-sm rounded-pill px-4">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <p class="text-muted small mb-0">
            <strong class="text-dark">{{ $jobs->total() }}</strong>
            {{ \Illuminate\Support\Str::plural('opportunity', $jobs->total()) }} found
        </p>
        <p class="text-muted small mb-0 d-none d-md-block">Verified employers updated daily</p>
    </div>

    <div class="row g-3">
        @forelse($jobs as $job)
            <div class="col-12 col-md-6 col-lg-4">
                <x-job-card :job="$job" />
            </div>
        @empty
            <div class="col-12">
                <x-empty-state title="No Jobs Match Your Filter" message="Try clearing or adjusting your search parameters." />
            </div>
        @endforelse
    </div>

    <x-pagination :paginator="$jobs" />
</div>
@endsection

@push('styles')
<style>
    .jobs-filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }
    .jobs-filter-bar .form-control,
    .jobs-filter-bar .form-select {
        width: auto;
        min-width: 150px;
    }
    .jobs-filter-bar #keyword {
        flex: 1 1 220px;
        min-width: 200px;
    }
    .jobs-filter-bar .form-check {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        margin: 0;
        padding-left: 1.1rem;
    }
    .jobs-filter-bar .form-check-input {
        margin: 0;
        flex-shrink: 0;
    }
</style>
@endpush