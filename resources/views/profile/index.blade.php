@extends('layouts.dashboard')

@section('title', 'My Profile - InternTrack')
@section('page_title', 'Student Profile & Resume Vault')

@section('content')
<div class="row g-4">
    <!-- Profile Form -->
    <div class="col-md-7">
        <div class="card card-custom bg-white p-4 mb-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-person-badge text-primary me-2"></i>Personal Information</h5>
            <form action="/profile" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                        <small class="text-muted">Email cannot be changed.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Roll / Student ID</label>
                        <input type="text" name="roll_no" class="form-control" value="{{ old('roll_no', $student->roll_no) }}" placeholder="e.g. 2021CS001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}" placeholder="+91 9876543210">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Academic Program</label>
                        <input type="text" name="program" class="form-control" value="{{ old('program', $student->program) }}" placeholder="e.g. B.Tech Computer Science">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Department</label>
                        <input type="text" name="department" class="form-control" value="{{ old('department', $student->department) }}" placeholder="e.g. Department of Computer Science">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Academic Year</label>
                        <select name="year" class="form-select">
                            <option value="">Select Year</option>
                            @foreach(['1st', '2nd', '3rd', '4th', '5th', 'Alumni'] as $yr)
                                <option value="{{ $yr }}" {{ old('year', $student->year) == $yr ? 'selected' : '' }}>{{ $yr }} Year</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            <i class="bi bi-check-circle me-1"></i> Save Profile Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resume Vault -->
    <div class="col-md-5">
        <div class="card card-custom bg-white p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-file-earmark-text text-success me-2"></i>Resume Vault</h5>

            <!-- Existing Resumes -->
            @forelse($student->resumes as $resume)
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <div class="fw-semibold text-dark">
                            {{ $resume->title }}
                            @if($resume->is_default)
                                <span class="badge bg-success ms-1">Default</span>
                            @endif
                        </div>
                        <small class="text-muted">Version {{ $resume->version }} &bull; Uploaded {{ $resume->created_at->format('M d, Y') }}</small>
                    </div>
                    <a href="{{ $resume->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </a>
                </div>
            @empty
                <div class="alert alert-warning small mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i> No resumes uploaded yet. Add your resume below.
                </div>
            @endforelse

            <!-- Upload New Resume -->
            <div class="mt-4">
                <h6 class="fw-semibold mb-3">Upload New Version</h6>
                <form action="/profile/resume" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Resume Title</label>
                        <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Software Internship Resume 2024" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Upload File (PDF/DOC)</label>
                        <input type="file" name="resume_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">— OR — Paste File URL</label>
                        <input type="url" name="file_url" class="form-control form-control-sm" placeholder="https://drive.google.com/...">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                        <label class="form-check-label small" for="is_default">Set as default resume</label>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm fw-semibold w-100">
                        <i class="bi bi-cloud-upload me-1"></i> Upload Resume
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
