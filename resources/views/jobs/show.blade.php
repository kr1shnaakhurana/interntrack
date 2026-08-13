@extends('layouts.app')

@section('title', $job->title . ' - InternTrack')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="/jobs" class="text-decoration-none text-muted small">&larr; Back to Opportunities</a>
    </div>

    <div class="row g-4">
        <!-- Main Job Details -->
        <div class="col-md-8">
            <div class="card card-custom p-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="fw-bold mb-1">{{ $job->title }}</h2>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5 fw-semibold text-secondary">{{ $job->company->name ?? 'External Company' }}</span>
                            @if(($job->company->latestVerification->status ?? '') === 'approved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                    <i class="bi bi-patch-check-fill me-1"></i> Verified
                                </span>
                            @endif
                        </div>
                    </div>
                    <x-status-badge :type="$job->job_type" />
                </div>

                <div class="d-flex flex-wrap gap-3 py-3 my-3 border-top border-bottom bg-light rounded-3 px-3">
                    <div>
                        <small class="text-muted d-block">LOCATION</small>
                        <span class="fw-semibold">{{ $job->location ?? 'Not Specified' }} {{ $job->is_remote ? '(Remote)' : '' }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">STIPEND / SALARY</small>
                        <span class="fw-semibold text-success">{{ $job->stipend_salary ?? 'Undisclosed' }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">DEADLINE</small>
                        <span class="fw-semibold">{{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('M d, Y') : 'Open until filled' }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">SOURCE</small>
                        @if($job->source && $job->source->type === 'direct')
                            <span class="badge bg-primary">InternTrack Direct</span>
                        @else
                            <span class="badge bg-secondary">External ({{ $job->source->name ?? 'Feed' }})</span>
                        @endif
                    </div>
                </div>

                <h5 class="fw-bold mt-4 mb-2">Job Description</h5>
                <div class="lh-lg text-secondary mb-4" style="white-space: pre-line;">
                    {{ $job->description }}
                </div>

                @if($job->skills_required)
                    <h6 class="fw-bold mb-2">Required Skills & Keywords</h6>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach(explode(',', $job->skills_required) as $skill)
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 fs-7">{{ trim($skill) }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Sidebar -->
        <div class="col-md-4">
            <div class="card card-custom p-4 bg-white sticky-top" style="top: 20px;">
                <h5 class="fw-bold mb-3">Application Actions</h5>

                @auth
                    @if(auth()->user()->isStudent())
                        @if($hasApplied)
                            <div class="alert alert-success d-flex align-items-center mb-3">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                                <div>You have already applied for this position.</div>
                            </div>
                            <a href="/applications" class="btn btn-outline-primary w-100 fw-semibold">View My Application</a>
                        @else
                            <!-- Submit Application Form -->
                            <form action="/jobs/{{ $job->id }}/apply" method="POST" class="mb-3">
                                @csrf
                                <div class="mb-3">
                                    <label for="cover_letter_url" class="form-label small fw-semibold">Resume / Cover Letter URL</label>
                                    <input type="url" name="cover_letter_url" class="form-control form-control-sm" placeholder="https://storage.com/my_resume.pdf">
                                </div>
                                <div class="mb-3">
                                    <label for="expected_salary" class="form-label small fw-semibold">Expected Stipend / Salary</label>
                                    <input type="text" name="expected_salary" class="form-control form-control-sm" placeholder="e.g. ₹40,000 / month">
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="bi bi-send me-1"></i> Submit Application
                                </button>
                            </form>
                        @endif

                        <form action="/jobs/{{ $job->id }}/save" method="POST" class="mt-2">
                            @csrf
                            <button type="submit" class="btn {{ $isSaved ? 'btn-secondary' : 'btn-outline-secondary' }} w-100 btn-sm">
                                <i class="bi {{ $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-1"></i>
                                {{ $isSaved ? 'Saved in Bookmarks' : 'Save Job' }}
                            </button>
                        </form>
                    @endif
                @else
                    <div class="alert alert-info small mb-3">
                        Please <a href="/login" class="fw-bold">login</a> or <a href="/register" class="fw-bold">create an account</a> as a student to apply or save jobs.
                    </div>
                @endauth

                @if($job->job_url)
                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted d-block mb-2">Original External Listing</small>
                        <a href="{{ $job->job_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-dark btn-sm w-100">
                            <i class="bi bi-box-arrow-up-right me-1"></i> View Original Listing
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
