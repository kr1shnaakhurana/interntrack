@extends('layouts.dashboard')

@section('title', 'Post New Job - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <h1 class="page-title">Post New Opportunity</h1>
    <a href="/company/jobs" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold text-secondary mb-4"><i class="bi bi-briefcase-fill text-primary me-2"></i>New Job / Internship Posting</h5>

                <form action="/company/jobs" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label for="title" class="form-label small fw-medium text-secondary">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-sm rounded-3" required value="{{ old('title') }}" placeholder="">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="job_type" class="form-label small fw-medium text-secondary">Opportunity Type <span class="text-danger">*</span></label>
                            <select name="job_type" id="job_type" class="form-select form-select-sm rounded-3" required>
                                <option value="">Select Type</option>
                                <option value="internship" {{ old('job_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                                <option value="full-time" {{ old('job_type') == 'full-time' ? 'selected' : '' }}>Full-Time</option>
                                <option value="part-time" {{ old('job_type') == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                                <option value="research" {{ old('job_type') == 'research' ? 'selected' : '' }}>Research</option>
                                <option value="fellowship" {{ old('job_type') == 'fellowship' ? 'selected' : '' }}>Fellowship</option>
                                <option value="government" {{ old('job_type') == 'government' ? 'selected' : '' }}>Government</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label small fw-medium text-secondary">Full Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control form-control-sm rounded-3" rows="7" required placeholder="">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="location" class="form-label small fw-medium text-secondary">Location</label>
                            <input type="text" name="location" id="location" class="form-control form-control-sm rounded-3" value="{{ old('location') }}" placeholder="">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="stipend_salary" class="form-label small fw-medium text-secondary">Stipend / Salary</label>
                            <input type="text" name="stipend_salary" id="stipend_salary" class="form-control form-control-sm rounded-3" value="{{ old('stipend_salary') }}" placeholder="">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="deadline" class="form-label small fw-medium text-secondary">Application Deadline</label>
                            <input type="date" name="deadline" id="deadline" class="form-control form-control-sm rounded-3" value="{{ old('deadline') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="skills_required" class="form-label small fw-medium text-secondary">Required Skills (comma-separated)</label>
                            <input type="text" name="skills_required" id="skills_required" class="form-control form-control-sm rounded-3" value="{{ old('skills_required') }}" placeholder="">
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote" value="1" {{ old('is_remote') ? 'checked' : '' }}>
                                <label class="form-check-label small fw-medium text-secondary" for="is_remote">This is a remote / work-from-home opportunity</label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2">
                                <i class="bi bi-send me-1"></i> Publish Opportunity
                            </button>
                            <a href="/company/jobs" class="btn btn-outline-secondary rounded-pill px-5 py-2">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection