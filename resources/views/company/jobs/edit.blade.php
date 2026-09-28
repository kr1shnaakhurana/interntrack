@extends('layouts.dashboard')

@section('title', 'Edit Job Posting - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <h1 class="page-title">Edit Job Posting</h1>
    <a href="/company/jobs" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold text-secondary mb-4"><i class="bi bi-pencil-square text-primary me-2"></i>Edit: {{ $job->title }}</h5>

                <form action="/company/jobs/{{ $job->id }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-12 col-md-8">
                            <label for="title" class="form-label small fw-medium text-secondary">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-sm rounded-3" required value="{{ old('title', $job->title) }}">
                        </div>
                        <div class="col-12 col-md-4">
                            <label for="job_type" class="form-label small fw-medium text-secondary">Opportunity Type <span class="text-danger">*</span></label>
                            <select name="job_type" id="job_type" class="form-select form-select-sm rounded-3" required>
                                @foreach(['internship','full-time','part-time','research','fellowship','government','other'] as $type)
                                    <option value="{{ $type }}" {{ old('job_type', $job->job_type) == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label small fw-medium text-secondary">Full Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control form-control-sm rounded-3" rows="7" required>{{ old('description', $job->description) }}</textarea>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="location" class="form-label small fw-medium text-secondary">Location</label>
                            <input type="text" name="location" id="location" class="form-control form-control-sm rounded-3" value="{{ old('location', $job->location) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="stipend_salary" class="form-label small fw-medium text-secondary">Stipend / Salary</label>
                            <input type="text" name="stipend_salary" id="stipend_salary" class="form-control form-control-sm rounded-3" value="{{ old('stipend_salary', $job->stipend_salary) }}">
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="deadline" class="form-label small fw-medium text-secondary">Application Deadline</label>
                            <input type="date" name="deadline" id="deadline" class="form-control form-control-sm rounded-3" value="{{ old('deadline', $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="skills_required" class="form-label small fw-medium text-secondary">Required Skills</label>
                            <input type="text" name="skills_required" id="skills_required" class="form-control form-control-sm rounded-3" value="{{ old('skills_required', $job->skills_required) }}">
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote" value="1" {{ old('is_remote', $job->is_remote) ? 'checked' : '' }}>
                                <label class="form-check-label small fw-medium text-secondary" for="is_remote">Remote / WFH Opportunity</label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2">
                                <i class="bi bi-check-circle me-1"></i> Save Changes
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