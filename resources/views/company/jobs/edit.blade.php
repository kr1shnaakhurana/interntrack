@extends('layouts.dashboard')

@section('title', 'Edit Job Posting - InternTrack')
@section('page_title', 'Edit Job Opportunity')

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
                <span class="fw-semibold fs-6 text-dark">Edit: {{ $job->title }}</span>
            </div>
            <div class="card-body p-4">
                <form action="/company/jobs/{{ $job->id }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label for="title" class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-sm" required value="{{ old('title', $job->title) }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="job_type" class="form-label">Opportunity Type <span class="text-danger">*</span></label>
                            <select name="job_type" id="job_type" class="form-select form-select-sm" required>
                                @foreach(['internship','full-time','part-time','research','fellowship','government','other'] as $type)
                                    <option value="{{ $type }}" {{ old('job_type', $job->job_type) == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Role Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control form-control-sm" rows="6" required>{{ old('description', $job->description) }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control form-control-sm" value="{{ old('location', $job->location) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="stipend_salary" class="form-label">Stipend / Compensation</label>
                            <input type="text" name="stipend_salary" id="stipend_salary" class="form-control form-control-sm" value="{{ old('stipend_salary', $job->stipend_salary) }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="deadline" class="form-label">Application Deadline</label>
                            <input type="date" name="deadline" id="deadline" class="form-control form-control-sm" value="{{ old('deadline', $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="skills_required" class="form-label">Required Skills (comma-separated)</label>
                            <input type="text" name="skills_required" id="skills_required" class="form-control form-control-sm" value="{{ old('skills_required', $job->skills_required) }}">
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote" value="1" {{ old('is_remote', $job->is_remote) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="is_remote">This is a fully remote position</label>
                            </div>
                        </div>

                        <div class="col-12 d-flex align-items-center justify-content-end gap-2 pt-3 border-top mt-3">
                            <a href="/company/jobs" class="btn btn-outline-secondary btn-sm">Cancel</a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg"></i> Update Job Posting
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection