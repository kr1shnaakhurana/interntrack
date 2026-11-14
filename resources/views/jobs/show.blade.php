@extends('layouts.app')

@section('title', $job->title . ' - InternTrack')

@section('content')
@php
    $student = auth()->check() && auth()->user()->isStudent() ? auth()->user()->student : null;
    $savedJobId = $student?->savedJobs()->where('job_id', $job->id)->value('id');
    $isExternal = ($job->source && $job->source->type === 'external') || $job->company?->is_external;
    $meta = is_array($job->metadata) ? $job->metadata : [];
@endphp

<div class="mb-3">
    <a href="/jobs" class="text-muted small fw-medium text-decoration-none d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Back to Opportunities
    </a>
</div>

<div class="row g-3">
    <!-- Main Job Details Column -->
    <div class="col-12 col-lg-8">
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h1 class="page-title fs-5 mb-1">{{ $job->title }}</h1>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <span class="fw-semibold text-secondary small">{{ $job->company->name ?? 'External Organization' }}</span>
                            @if(($job->company->latestVerification->status ?? '') === 'approved')
                                <span class="badge badge-subtle-primary">
                                    <i class="bi bi-patch-check-fill"></i> Verified Employer
                                </span>
                            @elseif(($job->company->sector ?? '') === 'government' || ($job->job_type ?? '') === 'government')
                                <span class="badge badge-subtle-success">
                                    <i class="bi bi-bank"></i> Government Recruitment
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <x-status-badge :type="$job->job_type" />
                    </div>
                </div>

                <!-- Source Attribution Alert -->
                @if($isExternal)
                    <div class="alert alert-light border d-flex align-items-center gap-2 py-2 px-3 my-3">
                        <i class="bi bi-info-circle text-primary fs-5"></i>
                        <div class="small">
                            <strong>Source Attribution:</strong> Listed via <strong>{{ $job->source->name ?? 'External Career Source' }}</strong>.
                            {{ $meta['attribution'] ?? 'Application and eligibility verification occur on their official portal.' }}
                        </div>
                    </div>
                @endif

                <!-- Government Notification Overview (if applicable) -->
                @if(! empty($meta['is_government']) || ($job->company->sector ?? '') === 'government' || ($job->job_type ?? '') === 'government')
                    <div class="card border-0 bg-light bg-opacity-75 mb-3 p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-bank fs-5 text-primary"></i>
                            <h2 class="fs-6 fw-bold mb-0 text-dark">Official Government Recruitment Overview</h2>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-6 col-md-4">
                                <span class="text-muted d-block">Department / Agency:</span>
                                <strong class="text-dark">{{ $meta['department'] ?? ($job->company->name ?? 'Government of India') }}</strong>
                            </div>
                            <div class="col-6 col-md-4">
                                <span class="text-muted d-block">Recruitment Category:</span>
                                <span class="badge badge-subtle-success">{{ $meta['government_category'] ?? 'Central / State Govt' }}</span>
                            </div>
                            <div class="col-6 col-md-4">
                                <span class="text-muted d-block">Vacancies Announced:</span>
                                <strong class="text-primary">{{ $meta['vacancies_count'] ?? 'Refer to Official PDF' }}</strong>
                            </div>
                            @if(! empty($meta['eligibility_criteria']))
                                <div class="col-12 col-md-8">
                                    <span class="text-muted d-block">Eligibility / Qualifications:</span>
                                    <span class="text-dark">{{ $meta['eligibility_criteria'] }}</span>
                                </div>
                            @endif
                            @if(! empty($meta['age_limit']))
                                <div class="col-6 col-md-4">
                                    <span class="text-muted d-block">Age Limit:</span>
                                    <span class="text-dark">{{ $meta['age_limit'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Structured Metadata Grid -->
                <div class="row g-3 py-3 my-2 border-top border-bottom bg-light bg-opacity-50 px-2 rounded-1">
                    <div class="col-6 col-md-3">
                        <div class="text-muted small" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">Location</div>
                        <div class="fw-medium text-dark small mt-0.5">
                            {{ $job->location ?? 'Not Specified' }}
                            @if($job->is_remote)
                                <span class="badge badge-subtle-success ms-1">Remote</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">Stipend / Salary</div>
                        <div class="fw-semibold text-success small mt-0.5">{{ $job->stipend_salary ?? 'Undisclosed' }}</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">Deadline</div>
                        <div class="fw-medium text-dark small mt-0.5">
                            {{ $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('M d, Y') : 'Open until filled' }}
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">Listing Source</div>
                        <div class="mt-0.5">
                            @if($job->source && $job->source->type === 'direct')
                                <span class="badge badge-subtle-primary">InternTrack Direct</span>
                            @else
                                <span class="badge badge-subtle-neutral">{{ $job->source->name ?? 'External Feed' }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h2 class="section-title fs-6 mb-2">Role Description</h2>
                    <div class="lh-base text-secondary small" style="white-space: pre-line;">{!! nl2br(e($job->description)) !!}</div>
                </div>

                @if($job->skills_required)
                    <div class="mt-4 pt-3 border-top">
                        <h3 class="section-title fs-6 mb-2">Required Skills &amp; Qualifications</h3>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(explode(',', $job->skills_required) as $skill)
                                <span class="skill-tag">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Application Sidebar Column -->
    <div class="col-12 col-lg-4">
        <div class="card sticky-top" style="top: 76px;">
            <div class="card-header py-3">
                <span class="fw-semibold fs-6 text-dark">Application Panel</span>
            </div>
            <div class="card-body p-3">
                @if($isExternal)
                    <!-- External Application Flow: Redirect to Official Source URL -->
                    <div class="alert alert-light border small text-muted p-2 mb-3">
                        <i class="bi bi-box-arrow-up-right text-primary me-1"></i>
                        This is an external / government opportunity. Applications are processed directly on the official host website.
                    </div>

                    @if($job->job_url)
                        <a href="{{ $job->job_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-100 py-2 mb-3 d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-box-arrow-up-right"></i> Apply on Official Portal
                        </a>
                    @else
                        <div class="alert alert-warning small py-2 mb-3">
                            Official application URL is currently unavailable.
                        </div>
                    @endif

                    @auth
                        @if(auth()->user()->isStudent())
                            @if($isSaved)
                                <form action="/saved-jobs/{{ $savedJobId }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-bookmark-fill text-primary"></i> Remove Bookmark
                                    </button>
                                </form>
                            @else
                                <form action="/jobs/{{ $job->id }}/save" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-bookmark"></i> Save Opportunity
                                    </button>
                                </form>
                            @endif
                        @endif
                    @else
                        <div class="alert alert-info py-2 px-3 mb-0">
                            <small>Want to bookmark this? Please <a href="/login" class="fw-semibold">log in</a> as a student.</small>
                        </div>
                    @endauth

                @else
                    <!-- Direct Internal Employer Flow: InternTrack Application -->
                    @auth
                        @if(auth()->user()->isStudent())
                            @if($hasApplied)
                                <div class="alert alert-info py-2 px-3 mb-3">
                                    <i class="bi bi-check-circle-fill fs-6 text-primary"></i>
                                    <div>
                                        <strong class="d-block">Application Submitted</strong>
                                        <span class="small">You have already submitted an application for this position.</span>
                                    </div>
                                </div>
                                <a href="/applications" class="btn btn-outline-primary w-100 mb-2">View My Applications</a>
                            @else
                                <form action="/jobs/{{ $job->id }}/apply" method="POST" class="mb-3">
                                    @csrf
                                    @if($student && $student->resumes->isNotEmpty())
                                        <div class="mb-2">
                                            <label for="saved_resume" class="form-label">Pick Saved Resume</label>
                                            <select id="saved_resume" class="form-select form-select-sm">
                                                <option value="">Select an uploaded resume...</option>
                                                @foreach($student->resumes as $resume)
                                                    <option value="{{ $resume->file_url }}">{{ $resume->title }} (v{{ $resume->version }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="mb-2">
                                        <label for="cover_letter_url" class="form-label">Resume / Portfolio Link</label>
                                        <input type="url" name="cover_letter_url" id="cover_letter_url" class="form-control form-control-sm" placeholder="https://example.com/resume.pdf">
                                    </div>

                                    <div class="mb-3">
                                        <label for="expected_salary" class="form-label">Expected Stipend / Compensation</label>
                                        <input type="text" name="expected_salary" id="expected_salary" class="form-control form-control-sm" placeholder="e.g. $1,500/mo or Disclosed">
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-send"></i> Submit Application
                                    </button>
                                </form>
                            @endif

                            @if($isSaved)
                                <form action="/saved-jobs/{{ $savedJobId }}" method="POST" class="mb-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-bookmark-fill text-primary"></i> Remove Bookmark
                                    </button>
                                </form>
                            @else
                                <form action="/jobs/{{ $job->id }}/save" method="POST" class="mb-2">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-bookmark"></i> Save Opportunity
                                    </button>
                                </form>
                            @endif
                        @endif
                    @else
                        <div class="alert alert-info py-2 px-3 mb-3">
                            <small>Please <a href="/login" class="fw-semibold">log in</a> or <a href="/register" class="fw-semibold">register</a> as a student to apply or save jobs.</small>
                        </div>
                    @endauth

                    @if($job->job_url)
                        <div class="pt-3 border-top">
                            <a href="{{ $job->job_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-box-arrow-up-right"></i> View External Source
                            </a>
                        </div>
                    @endif
                @endif
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
