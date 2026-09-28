@extends('layouts.app')

@section('title', 'My Profile - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <h1 class="page-title">Profile & Resume Vault</h1>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-6 d-flex">
        <div class="card border-0 shadow-sm w-100 h-100">
            <div class="card-body p-4 p-md-5 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-4">
                    @php
                        $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                    @endphp
                    <div class="avatar-initials rounded-circle bg-primary-light text-primary fw-bold flex-shrink-0">
                        {{ $initials ?: 'ST' }}
                    </div>
                    <div class="text-truncate">
                        <h5 class="fw-bold text-dark mb-1 text-truncate">Personal Information</h5>
                        <small class="text-muted">{{ $student->program ?? $user->email }}</small>
                    </div>
                </div>
                <form action="/profile" method="POST" class="d-flex flex-column flex-grow-1">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label small fw-medium text-secondary">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control form-control-sm rounded-3" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label small fw-medium text-secondary">Email Address</label>
                            <input type="email" id="email" class="form-control form-control-sm rounded-3 bg-light" value="{{ $user->email }}" disabled>
                            <div class="form-text text-muted">Email cannot be changed.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="roll_no" class="form-label small fw-medium text-secondary">Roll / Student ID</label>
                            <input type="text" name="roll_no" id="roll_no" class="form-control form-control-sm rounded-3" value="{{ old('roll_no', $student->roll_no) }}" placeholder="">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label small fw-medium text-secondary">Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control form-control-sm rounded-3" value="{{ old('phone', $student->phone) }}" placeholder="">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="program" class="form-label small fw-medium text-secondary">Academic Program</label>
                            <input type="text" name="program" id="program" class="form-control form-control-sm rounded-3" value="{{ old('program', $student->program) }}" placeholder="">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="department" class="form-label small fw-medium text-secondary">Department</label>
                            <input type="text" name="department" id="department" class="form-control form-control-sm rounded-3" value="{{ old('department', $student->department) }}" placeholder="">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="year" class="form-label small fw-medium text-secondary">Academic Year</label>
                            <select name="year" id="year" class="form-select form-select-sm rounded-3">
                                <option value="">Select Year</option>
                                @foreach(['1st', '2nd', '3rd', '4th', '5th', 'Alumni'] as $yr)
                                    <option value="{{ $yr }}" {{ old('year', $student->year) == $yr ? 'selected' : '' }}>{{ $yr }} Year</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-auto pt-4 text-center">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2">
                            <i class="bi bi-check-circle me-1"></i> Save Profile Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6 d-flex">
        <div class="card border-0 shadow-sm w-100 h-100">
            <div class="card-body p-4 p-md-5 d-flex flex-column">
                <h5 class="fw-bold text-secondary mb-4"><i class="bi bi-file-earmark-text text-success me-2"></i>Resume Vault</h5>

                @forelse($student->resumes as $resume)
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 py-3 border-bottom">
                        <div>
                            <div class="fw-semibold text-dark">
                                {{ $resume->title }}
                                @if($resume->is_default)
                                    <span class="badge bg-primary-light text-primary rounded-pill ms-2">Default</span>
                                @endif
                            </div>
                            <small class="text-muted">Version {{ $resume->version }} &bull; Uploaded {{ $resume->created_at->format('M d, Y') }}</small>
                        </div>
                        <a href="{{ $resume->file_url }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                            <i class="bi bi-file-earmark-pdf me-1"></i> View
                        </a>
                    </div>
                @empty
                    <div class="alert alert-warning alert-dismissible fade show rounded-3 border-0 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i> <small>No resumes uploaded yet. Add your resume below.</small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforelse

                <div class="pt-4 mt-auto">
                    <h5 class="fw-bold text-primary mb-4"><i class="bi bi-cloud-upload me-2"></i>Upload New Version</h5>
                    <form action="/profile/resume" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label small fw-medium text-secondary">Resume Title</label>
                            <input type="text" name="title" id="title" class="form-control form-control-sm rounded-3" placeholder="" required>
                        </div>
                        <div class="mb-3">
                            <label for="resume_file" class="form-label small fw-medium text-secondary">Upload File (PDF/DOC)</label>
                            <input type="file" name="resume_file" id="resume_file" class="form-control form-control-sm rounded-3" accept=".pdf,.doc,.docx">
                        </div>
                        <div class="mb-3">
                            <label for="file_url" class="form-label small fw-medium text-secondary">OR Paste File URL</label>
                            <input type="url" name="file_url" id="file_url" class="form-control form-control-sm rounded-3" placeholder="">
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                            <label class="form-check-label small text-secondary" for="is_default">Set as default resume</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2">
                            <i class="bi bi-cloud-upload me-1"></i> Upload Resume
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-initials {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        font-size: 1.25rem;
    }
</style>
@endpush