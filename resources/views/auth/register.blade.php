@extends('layouts.app')

@section('title', 'Register - InternTrack')

@section('content')
<div class="auth-form container w-100">
    <div class="auth-card p-4 p-md-5">
        <div class="text-center mb-4">
            <h2 class="page-title text-dark fw-bold mb-1">Create your account</h2>
            <p class="text-muted mb-0">Join InternTrack to start your journey</p>
        </div>

        <form action="/register" method="POST">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 py-2 px-3 mb-4" role="alert">
                    <small>{{ $errors->first() }}</small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="mb-3">
                <label for="name" class="form-label small fw-semibold text-dark mb-1">Full Name / Organization Name</label>
                <input type="text" class="form-control rounded-3" id="name" name="name" value="{{ old('name') }}" required placeholder="">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-dark mb-1">Email Address</label>
                <input type="email" class="form-control rounded-3" id="email" name="email" value="{{ old('email') }}" required placeholder="">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label for="password" class="form-label small fw-semibold text-dark mb-1">Password</label>
                    <input type="password" class="form-control rounded-3" id="password" name="password" required placeholder="">
                </div>
                <div class="col-6">
                    <label for="password_confirmation" class="form-label small fw-semibold text-dark mb-1">Confirm Password</label>
                    <input type="password" class="form-control rounded-3" id="password_confirmation" name="password_confirmation" required placeholder="">
                </div>
            </div>

            <div class="mb-4">
                <label for="role" class="form-label small fw-semibold text-dark mb-1">Account Role</label>
                    <select class="form-select rounded-3" id="role" name="role" required onchange="toggleRoleFields(this.value)">
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student / Candidate</option>
                    <option value="company" {{ old('role') == 'company' ? 'selected' : '' }}>Employer / Organization</option>
                </select>
            </div>

            <div id="student-fields" class="mb-3">
                <label for="program" class="form-label small fw-semibold text-dark mb-1">Program</label>
                <input type="text" class="form-control rounded-3" id="program" name="program" placeholder="">
            </div>

            <div id="company-fields" class="mb-3" hidden>
                <label for="company_name" class="form-label small fw-semibold text-dark mb-1">Organization Name</label>
                <input type="text" class="form-control rounded-3" id="company_name" name="company_name" placeholder="">
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold mb-4">
                Create Account
            </button>
        </form>

        <div class="text-center">
            <span class="text-secondary">Already have an account?</span>
            <a href="/login" class="text-primary text-decoration-none fw-semibold ms-1">Sign In</a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    :root {
        --navy: #0f172a;
        --navy-light: #1e293b;
        --slate-muted: #64748b;
    }
    body { font-family: 'Inter', system-ui, sans-serif; background: #f8fafc; }
    .navbar-brand { font-weight: 800; font-size: 1.5rem; color: var(--navy) !important; }
    .auth-form { max-width: 520px; }
    .auth-card { background: #fff; border: 1px solid var(--border-color); border-radius: 1rem; box-shadow: var(--shadow-sm); }
</style>
@endpush