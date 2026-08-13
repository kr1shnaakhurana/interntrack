@extends('layouts.auth')

@section('title', 'Login - InternTrack')

@section('content')
<form action="/login" method="POST">
    @csrf

    <div class="mb-3">
        <label for="email" class="form-label text-light small fw-semibold">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="name@domain.com">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label text-light small fw-semibold">Password</label>
        <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
    </div>

    <div class="mb-4 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label text-muted small" for="remember">Remember me on this device</label>
    </div>

    <button type="submit" class="btn btn-primary-gradient w-100 py-2 mb-3">
        Sign In to Account
    </button>

    <div class="text-center">
        <span class="text-muted small">Don't have an account?</span>
        <a href="/register" class="text-primary text-decoration-none small fw-semibold ms-1">Create Account</a>
    </div>
</form>
