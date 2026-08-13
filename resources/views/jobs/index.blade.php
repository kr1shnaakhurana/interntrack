@extends('layouts.app')

@section('title', 'Explore Jobs & Internships - InternTrack')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold mb-1">Opportunity Discovery</h2>
            <p class="text-muted">Search through verified direct employer postings and aggregated external feeds.</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card card-custom p-3 mb-4 bg-white">
        <form action="/jobs" method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" placeholder="Search title, skills, keyword..." value="{{ request('keyword') }}">
            </div>
            <div class="col-md-2">
                <select name="job_type" class="form-select">
                    <option value="">All Job Types</option>
                    <option value="internship" {{ request('job_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                    <option value="full-time" {{ request('job_type') == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                    <option value="part-time" {{ request('job_type') == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                    <option value="research" {{ request('job_type') == 'research' ? 'selected' : '' }}>Research</option>
                    <option value="fellowship" {{ request('job_type') == 'fellowship' ? 'selected' : '' }}>Fellowship</option>
                    <option value="government" {{ request('job_type') == 'government' ? 'selected' : '' }}>Government</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="location" class="form-control" placeholder="Location..." value="{{ request('location') }}">
            </div>
            <div class="col-md-2">
                <select name="sector" class="form-select">
                    <option value="">All Sectors</option>
                    <option value="private" {{ request('sector') == 'private' ? 'selected' : '' }}>Private</option>
                    <option value="government" {{ request('sector') == 'government' ? 'selected' : '' }}>Government</option>
                    <option value="ngo" {{ request('sector') == 'ngo' ? 'selected' : '' }}>NGO</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="/jobs" class="btn btn-outline-secondary">Reset</a>
            </div>

            <div class="col-12 d-flex gap-4 pt-2 border-top">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_remote" value="1" id="is_remote" {{ request('is_remote') ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="is_remote">Remote Only</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="verified_only" value="1" id="verified_only" {{ request('verified_only') ? 'checked' : '' }}>
                    <label class="form-check-label small fw-semibold" for="verified_only">Verified Employers Only</label>
                </div>
            </div>
        </form>
    </div>

    <!-- Jobs Grid -->
    <div class="row g-4">
        @forelse($jobs as $job)
            <div class="col-md-4">
                <x-job-card :job="$job" />
            </div>
        @empty
            <div class="col-12">
                <x-empty-state title="No Jobs Match Your Filter" message="Try clearing or adjusting your search parameters." />
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <x-pagination :paginator="$jobs" />
</div>
@endsection
