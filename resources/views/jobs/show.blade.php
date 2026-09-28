@extends('layouts.app')

@section('title', $job->title . ' - InternTrack')

@section('content')
@php
    $student = auth()->check() && auth()->user()->isStudent() ? auth()->user()->student : null;
    $savedJobId = $student?->savedJobs()->where('job_id', $job->id)->value('id');
@endphp

<div class="container">
    <div class="mb-4">
        <a href="/jobs" class="text-decoration-none text-muted small fw-medium">&larr; Back to Opportunities</a>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8 d-flex">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden w-100 h-100">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                        <div>
                            <h2 class="fw-bold text-dark mb-2">{{ $job->title }}</h2>
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="fw-semibold text-secondary">{{ $job->company->name ?? 'External Company' }}</span>
                                @if(($job->company->latestVerification->status ?? '') === 'approved')
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                                        <i class="bi bi-patch-check-fill me-1"></i> Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                        <x-status-badge :type="$job->job_type" />
                    </div>

                    <div class="row g-3 mb-4 pb-4 border-bottom">
                        <div class="col-12 col-sm-6">
                            <small class="text-muted d-block text-uppercase fw-medium">Location</small>
                            <span class="fw-semibold text-dark">{{ $job->location ?? 'Not Specified' }} {{ $job->is_remote ? '<span class="badge bg-success bg-opacity-10 text-success ms-2 rounded-pill px-2 py-1">Remote</span>' : '' }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <small class="text-muted d-block text-uppercase fw-medium">Stipend / Salary</small>
                            <span class="fw-semibold text-success">{{ $job->stipend_salary ?? 'Undisclosed' }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <small class="text-muted d-block text-uppercase fw-medium">Deadline</small>
                            <span class="fw-semibold text-dark">{{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('M d, Y') : 'Open until filled' }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <small class="text-muted d-block text-uppercase fw-medium">Source</small>
                            @if($job->source && $job->source->type === 'direct')
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">InternTrack Direct</span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1">External ({{ $job->source->name ?? 'Feed' }})</span>
                            @endif
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3">Job Description</h5>
                    <div class="lh-base text-secondary mb-4 whitespace-pre-wrap">
                        {{ $job->description }}
                    </div>

                    @if($job->skills_required)
                        <h6 class="fw-bold mb-2 text-secondary">Required Skills & Keywords</h6>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @foreach(explode(',', $job->skills_required) as $skill)
                                <span class="badge bg-light text-dark rounded-pill px-3 py-2 small">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top sticky-20">
                <div class="card-body p-4 p-md-5">
                    <h5 class="fw-bold text-secondary mb-4">Application Actions</h5>

                    @auth
                        @if(auth()->user()->isStudent())
                            @if($hasApplied)
                                <div class="alert alert-primary alert-dismissible fade show rounded-3 border-0 mb-4" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                        <div>
                                            <strong>Already Applied</strong>
                                            <div class="small">You have already applied for this position.</div>
                                        </div>
                                    </div>
                                </div>
                                <a href="/applications" class="btn btn-outline-primary w-100 rounded-pill py-2 mb-3">View My Application</a>
                            @else
                                <form action="/jobs/{{ $job->id }}/apply" method="POST" class="mb-4">
                                    @csrf
                                    @if($student && $student->resumes->isNotEmpty())
                                        <div class="mb-2">
                                            <label for="saved_resume" class="form-label small fw-medium text-secondary">Pick From Your Resumes</label>
                                            <select id="saved_resume" class="form-select form-select-sm rounded-3">
                                                <option value="">Select an uploaded resume...</option>
                                                @foreach($student->resumes as $resume)
                                                    <option value="{{ $resume->file_url }}">{{ $resume->title }} (v{{ $resume->version }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label for="cover_letter_url" class="form-label small fw-medium text-secondary">Resume / Cover Letter URL</label>
                                        <input type="url" name="cover_letter_url" id="cover_letter_url" class="form-control form-control-sm rounded-3" placeholder="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="expected_salary" class="form-label small fw-medium text-secondary">Expected Stipend / Salary</label>
                                        <input type="text" name="expected_salary" id="expected_salary" class="form-control form-control-sm rounded-3" placeholder="">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                                        <i class="bi bi-send me-1"></i> Submit Application
                                    </button>
                                </form>
                            @endif

                            @if($isSaved)
                                <form action="/saved-jobs/{{ $savedJobId }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-pill py-2">
                                        <i class="bi bi-bookmark-fill me-1"></i> Remove From Bookmarks
                                    </button>
                                </form>
                            @else
                                <form action="/jobs/{{ $job->id }}/save" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary w-100 rounded-pill py-2">
                                        <i class="bi bi-bookmark me-1"></i> Save Job
                                    </button>
                                </form>
                            @endif
                        @endif
                    @else
                        <div class="alert alert-primary alert-dismissible fade show rounded-3 border-0 mb-3" role="alert">
                            <small>Please <a href="/login" class="fw-bold">login</a> or <a href="/register" class="fw-bold">create an account</a> as a student to apply or save jobs.</small>
                        </div>
                    @endauth

                    @if($job->job_url)
                        <div class="mt-4 pt-4 border-top">
                            <small class="text-muted d-block mb-2">Original External Listing</small>
                            <a href="{{ $job->job_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary w-100 rounded-pill py-2">
                                <i class="bi bi-box-arrow-up-right me-1"></i> View Original Listing
                            </a>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-top">
                        <h6 class="fw-bold text-secondary mb-3">Submission Info</h6>
                        <div class="small text-muted">
                            <div class="mb-2"><strong>Location:</strong> {{ $job->location ?? 'Not Specified' }} {{ $job->is_remote ? ' (Remote)' : '' }}</div>
                            <div class="mb-2"><strong>Stipend/Salary:</strong> {{ $job->stipend_salary ?? 'Undisclosed' }}</div>
                            <div class="mb-2"><strong>Deadline:</strong> {{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('M d, Y') : 'Open until filled' }}</div>
                            <div><strong>Source:</strong> @if($job->source && $job->source->type === 'direct')InternTrack Direct@else{{ $job->source->name ?? 'External' }}@endif</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const resumePicker = document.getElementById('saved_resume');
    const resumeField = document.getElementById('cover_letter_url');

    if (resumePicker && resumeField) {
        resumePicker.addEventListener('change', function () {
            if (resumePicker.value) {
                resumeField.value = resumePicker.value;
            }
        });
    }
</script>
@endpush