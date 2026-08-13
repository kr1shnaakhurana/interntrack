@extends('layouts.dashboard')

@section('title', 'Post New Job - InternTrack')
@section('page_title', 'Post New Opportunity')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom bg-white p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-briefcase-fill text-primary me-2"></i>New Job / Internship Posting</h5>

            <form action="/company/jobs" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Job Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required
                            value="{{ old('title') }}" placeholder="e.g. Software Engineering Intern">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Opportunity Type <span class="text-danger">*</span></label>
                        <select name="job_type" class="form-select" required>
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
                        <label class="form-label fw-semibold small">Full Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="7" required
                            placeholder="Describe responsibilities, requirements, perks, selection process...">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Location</label>
                        <input type="text" name="location" class="form-control"
                            value="{{ old('location') }}" placeholder="e.g. Bengaluru, Karnataka">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Stipend / Salary</label>
                        <input type="text" name="stipend_salary" class="form-control"
                            value="{{ old('stipend_salary') }}" placeholder="e.g. ₹25,000/month or Unpaid">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Application Deadline</label>
                        <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Required Skills (comma-separated)</label>
                        <input type="text" name="skills_required" class="form-control"
                            value="{{ old('skills_required') }}" placeholder="e.g. Python, React, SQL">
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote"
                                value="1" {{ old('is_remote') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small" for="is_remote">
                                This is a remote / work-from-home opportunity
                            </label>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            <i class="bi bi-send me-1"></i> Publish Opportunity
                        </button>
                        <a href="/company/jobs" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
