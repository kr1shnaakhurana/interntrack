@extends('layouts.app')

@section('title', 'Login - InternTrack')

@section('content')
<div class="auth-form container w-100">
    <div class="auth-card p-4 p-md-5">
        <div class="text-center mb-4">
            <h2 class="page-title text-dark fw-bold mb-1">Welcome back</h2>
            <p class="text-muted mb-0">Sign in to your InternTrack account</p>
        </div>

        <form action="/login" method="POST">
            @csrf

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 py-2 px-3 mb-4" role="alert">
                    <small>{{ session('status') }}</small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 py-2 px-3 mb-4" role="alert">
                    <small>{{ $errors->first() }}</small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="mb-3">
                <label for="email" class="form-label small fw-semibold text-dark mb-1">Email Address</label>
                <input type="email" class="form-control rounded-3" id="email" name="email" value="{{ old('email') }}" required placeholder="">
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-semibold text-dark mb-1">Password</label>
                <input type="password" class="form-control rounded-3" id="password" name="password" required placeholder="">
            </div>

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label small text-secondary" for="remember">Remember me</label>
                </div>
                <a href="/forgot-password" class="small text-primary text-decoration-none fw-medium">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold mb-4">
                Sign In
            </button>
        </form>

        <div class="text-center">
            <span class="text-secondary">Don't have an account?</span>
            <a href="/register" class="text-primary text-decoration-none fw-semibold ms-1">Create Account</a>
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
    .auth-form { max-width: 480px; }
    .auth-card { background: #fff; border: 1px solid var(--border-color); border-radius: 1rem; box-shadow: var(--shadow-sm); }
</style>
@endpush