@extends('layouts.dashboard')

@section('title', 'Edit Job Posting - InternTrack')
@section('page_title', 'Edit Job Posting')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-custom bg-white p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-pencil-square text-primary me-2"></i>Edit: {{ $job->title }}</h5>

            <form action="/company/jobs/{{ $job->id }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Job Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required value="{{ old('title', $job->title) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Opportunity Type <span class="text-danger">*</span></label>
                        <select name="job_type" class="form-select" required>
                            @foreach(['internship','full-time','part-time','research','fellowship','government','other'] as $type)
                                <option value="{{ $type }}" {{ old('job_type', $job->job_type) == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Full Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="7" required>{{ old('description', $job->description) }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Location</label>
                        <input type="text" name="location" class="form-control" value="{{ old('location', $job->location) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Stipend / Salary</label>
                        <input type="text" name="stipend_salary" class="form-control" value="{{ old('stipend_salary', $job->stipend_salary) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Application Deadline</label>
                        <input type="date" name="deadline" class="form-control"
                            value="{{ old('deadline', $job->deadline ? \Carbon\Carbon::parse($job->deadline)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Required Skills</label>
                        <input type="text" name="skills_required" class="form-control" value="{{ old('skills_required', $job->skills_required) }}">
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote"
                                value="1" {{ old('is_remote', $job->is_remote) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small" for="is_remote">Remote / WFH Opportunity</label>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            <i class="bi bi-check-circle me-1"></i> Save Changes
                        </button>
                        <a href="/company/jobs" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
