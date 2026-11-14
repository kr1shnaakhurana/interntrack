@extends('layouts.app')

@section('title', 'Profile & Resumes - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Candidate Profile &amp; Resume Vault</h1>
        <p class="text-muted small mb-0">Manage your candidate identity, university affiliation, and uploaded resumes.</p>
    </div>
</div>

<div class="row g-3">
    <!-- Personal Information Card -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header py-3">
                <span class="fw-semibold fs-6 text-dark">Personal Information</span>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    @php
                        $initials = collect(explode(' ', trim($user->name)))->filter()->take(2)->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('');
                    @endphp
                    <div class="bg-primary text-white d-flex align-items-center justify-content-center fw-semibold" style="width: 44px; height: 44px; border-radius: 4px; font-size: 16px;">
                        {{ $initials ?: 'ST' }}
                    </div>
                    <div class="min-w-0">
                        <div class="fw-semibold text-dark">{{ $user->name }}</div>
                        <div class="text-muted small">{{ $user->email }} &bull; {{ $student->program ?? 'Student Candidate' }}</div>
                    </div>
                </div>

                <form action="/profile" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control form-control-sm" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" class="form-control form-control-sm bg-light" value="{{ $user->email }}" disabled>
                            <div class="text-muted" style="font-size: 11px;">Primary login email cannot be changed.</div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="roll_no" class="form-label">Student / Roll ID</label>
                            <input type="text" name="roll_no" id="roll_no" class="form-control form-control-sm" value="{{ old('roll_no', $student->roll_no) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control form-control-sm" value="{{ old('phone', $student->phone) }}">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="program" class="form-label">Academic Program</label>
                            <input type="text" name="program" id="program" class="form-control form-control-sm" value="{{ old('program', $student->program) }}" placeholder="e.g. B.Tech Computer Science">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" name="department" id="department" class="form-control form-control-sm" value="{{ old('department', $student->department) }}" placeholder="e.g. Engineering">
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="year" class="form-label">Current Academic Year</label>
                            <select name="year" id="year" class="form-select form-select-sm">
                                <option value="">Select Year</option>
                                @foreach(['1st', '2nd', '3rd', '4th', '5th', 'Alumni'] as $yr)
                                    <option value="{{ $yr }}" {{ old('year', $student->year) == $yr ? 'selected' : '' }}>{{ $yr }} Year</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-top mt-4 text-end">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-check-lg"></i> Save Profile Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resume Vault Card -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header py-3">
                <span class="fw-semibold fs-6 text-dark">Resume Vault</span>
            </div>
            <div class="card-body p-4 d-flex flex-column">
                <div class="mb-4">
                    <h2 class="section-title fs-6 mb-2">Uploaded Resumes</h2>
                    @forelse($student->resumes as $resume)
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 py-2 border-bottom">
                            <div>
                                <div class="fw-semibold text-dark small">
                                    {{ $resume->title }}
                                    @if($resume->is_default)
                                        <span class="badge badge-subtle-primary ms-1">Default</span>
                                    @endif
                                </div>
                                <div class="text-muted" style="font-size: 11.5px;">
                                    Version {{ $resume->version }} &bull; Uploaded {{ $resume->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            <a href="{{ $resume->file_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i> View File
                            </a>
                        </div>
                    @empty
                        <div class="p-3 bg-light border rounded-1 text-muted small text-center mb-3">
                            No resumes uploaded yet. Upload your first version below.
                        </div>
                    @endforelse
                </div>

                <div class="mt-auto pt-3 border-top">
                    <h3 class="section-title fs-6 mb-3">Upload New Resume</h3>
                    <form action="/profile/resume" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-2">
                            <label for="title" class="form-label">Resume Title</label>
                            <input type="text" name="title" id="title" class="form-control form-control-sm" placeholder="e.g. Software Engineer Resume 2026" required>
                        </div>
                        <div class="mb-2">
                            <label for="resume_file" class="form-label">Upload File (PDF/DOC)</label>
                            <input type="file" name="resume_file" id="resume_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx">
                        </div>
                        <div class="mb-2">
                            <label for="file_url" class="form-label">OR Hosted Document URL</label>
                            <input type="url" name="file_url" id="file_url" class="form-control form-control-sm" placeholder="https://example.com/my-resume.pdf">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                            <label class="form-check-label small" for="is_default">Set as default application resume</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-cloud-upload"></i> Upload to Vault
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection