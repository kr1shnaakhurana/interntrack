@extends('layouts.app')

@section('title', 'Sign In - InternTrack')

@section('content')
<div class="row justify-content-center py-4 py-md-5">
    <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h1 class="page-title fs-5 mb-1">Welcome back</h1>
                <p class="text-muted small mb-0">Sign in to your InternTrack account</p>
            </div>

            <form action="/login" method="POST">
                @csrf

                @if(session('status'))
                    <div class="alert alert-success py-2 px-3 mb-3">
                        <small>{{ session('status') }}</small>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger py-2 px-3 mb-3">
                        <small>{{ $errors->first() }}</small>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control form-control-sm" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@domain.com">
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label mb-0">Password</label>
                        <a href="/forgot-password" class="small text-muted text-decoration-none" style="font-size: 11.5px;">Forgot password?</a>
                    </div>
                    <input type="password" class="form-control form-control-sm" id="password" name="password" required placeholder="••••••••">
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label small" for="remember">Keep me signed in</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    Sign In
                </button>
            </form>

            <div class="text-center pt-3 border-top">
                <span class="text-muted small">New job seeker?</span>
                <a href="/register" class="small fw-semibold ms-1">Create Account</a>
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