@extends('layouts.app')

@section('title', 'Create Student Account - InternTrack')

@section('content')
<div class="row justify-content-center py-4 py-md-5">
    <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h1 class="page-title fs-5 mb-1">Create your account</h1>
                <p class="text-muted small mb-0">Join InternTrack to discover opportunities and manage your applications</p>
            </div>

            <form action="/register" method="POST">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger py-2 px-3 mb-3">
                        <small>{{ $errors->first() }}</small>
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success py-2 px-3 mb-3">
                        <small>{{ session('success') }}</small>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="e.g. Aarav Sharma">
                    @error('name')<div class="invalid-feedback"><small>{{ $message }}</small></div>@enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="name@university.edu">
                    @error('email')<div class="invalid-feedback"><small>{{ $message }}</small></div>@enderror
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control form-control-sm @error('password') is-invalid @enderror" id="password" name="password" required placeholder="••••••••">
                        @error('password')<div class="invalid-feedback"><small>{{ $message }}</small></div>@enderror
                    </div>
                    <div class="col-6">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control form-control-sm" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="program" class="form-label">Academic Program <span class="text-muted small">(optional)</span></label>
                    <input type="text" class="form-control form-control-sm" id="program" name="program" value="{{ old('program') }}" placeholder="e.g. B.Tech Computer Science">
                </div>

                <div class="mb-4">
                    <label for="roll_no" class="form-label">Roll Number <span class="text-muted small">(optional)</span></label>
                    <input type="text" class="form-control form-control-sm" id="roll_no" name="roll_no" value="{{ old('roll_no') }}" placeholder="e.g. CS2026-001">
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    Create Account
                </button>
            </form>

            <div class="text-center pt-3 border-top">
                <span class="text-muted small">Already have an account?</span>
                <a href="/login" class="small fw-semibold ms-1">Sign In</a>
            </div>

            <div class="text-center mt-2">
                <a href="/company/register" class="text-muted small text-decoration-none">
                    <i class="bi bi-building me-1"></i> Hiring? Register your company &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection