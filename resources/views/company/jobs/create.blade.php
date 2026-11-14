@extends('layouts.dashboard')

@section('title', 'Post New Opportunity - InternTrack')
@section('page_title', 'Create New Posting')

@section('content')
<div class="mb-3">
    <a href="/company/jobs" class="text-muted small fw-medium text-decoration-none d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Back to Job Postings
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card">
            <div class="card-header py-3">
                <span class="fw-semibold fs-6 text-dark">Job Details &amp; Specifications</span>
            </div>
            <div class="card-body p-4">
                <form action="/company/jobs" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label for="title" class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-sm" required value="{{ old('title') }}" placeholder="e.g. Frontend Engineering Intern">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="job_type" class="form-label">Opportunity Type <span class="text-danger">*</span></label>
                            <select name="job_type" id="job_type" class="form-select form-select-sm" required>
                                <option value="">Select Opportunity Type</option>
                                <option value="internship" {{ old('job_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                                <option value="full-time" {{ old('job_type') == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                                <option value="part-time" {{ old('job_type') == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                                <option value="research" {{ old('job_type') == 'research' ? 'selected' : '' }}>Research</option>
                                <option value="fellowship" {{ old('job_type') == 'fellowship' ? 'selected' : '' }}>Fellowship</option>
                                <option value="government" {{ old('job_type') == 'government' ? 'selected' : '' }}>Government</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Role Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control form-control-sm" rows="6" required placeholder="Outline key responsibilities, deliverables, and expectations...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control form-control-sm" value="{{ old('location') }}" placeholder="e.g. San Francisco, CA or Hybrid">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="stipend_salary" class="form-label">Stipend / Compensation</label>
                            <input type="text" name="stipend_salary" id="stipend_salary" class="form-control form-control-sm" value="{{ old('stipend_salary') }}" placeholder="e.g. $2,000 / month">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="deadline" class="form-label">Application Deadline</label>
                            <input type="date" name="deadline" id="deadline" class="form-control form-control-sm" value="{{ old('deadline') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="skills_required" class="form-label">Required Skills (comma-separated)</label>
                            <input type="text" name="skills_required" id="skills_required" class="form-control form-control-sm" value="{{ old('skills_required') }}" placeholder="e.g. React, TypeScript, Git">
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote" value="1" {{ old('is_remote') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="is_remote">This is a fully remote position</label>
                            </div>
                        </div>

                        <div class="col-12 d-flex align-items-center justify-content-end gap-2 pt-3 border-top mt-3">
                            <a href="/company/jobs" class="btn btn-outline-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg"></i> Publish Opportunity
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection