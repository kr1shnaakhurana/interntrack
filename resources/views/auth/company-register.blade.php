@extends('layouts.app')

@section('title', 'Employer Registration - InternTrack')

@section('content')
<div class="row justify-content-center py-4 py-md-5">
    <div class="col-12 col-sm-10 col-md-8 col-lg-7 col-xl-6">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h1 class="page-title fs-5 mb-1">Register your company</h1>
                <p class="text-muted small mb-0">Set up your employer account to post opportunities and manage applicants</p>
            </div>

            <form action="/company/register" method="POST">
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

                {{-- Account details --}}
                <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing: 0.04em; font-size: 10.5px;">Account Details</p>

                <div class="mb-3">
                    <label for="name" class="form-label">Your Full Name</label>
                    <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="e.g. Priya Mehra">
                    @error('name')<div class="invalid-feedback"><small>{{ $message }}</small></div>@enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Work Email Address</label>
                    <input type="email" class="form-control form-control-sm @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required placeholder="hr@company.com">
                    @error('email')<div class="invalid-feedback"><small>{{ $message }}</small></div>@enderror
                </div>

                <div class="row g-2 mb-4">
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

                {{-- Company details --}}
                <p class="text-muted small fw-semibold text-uppercase mb-2" style="letter-spacing: 0.04em; font-size: 10.5px;">Company Details</p>

                <div class="mb-3">
                    <label for="company_name" class="form-label">Company / Organization Name</label>
                    <input type="text" class="form-control form-control-sm @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" required placeholder="e.g. Nexus Innovations Ltd.">
                    @error('company_name')<div class="invalid-feedback"><small>{{ $message }}</small></div>@enderror
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="industry" class="form-label">Industry</label>
                        <input type="text" class="form-control form-control-sm @error('industry') is-invalid @enderror" id="industry" name="industry" value="{{ old('industry') }}" placeholder="e.g. Technology, Finance">
                        @error('industry')<div class="invalid-feedback"><small>{{ $message }}</small></div>@enderror
                    </div>
                    <div class="col-6">
                        <label for="size" class="form-label">Company Size</label>
                        <select class="form-select form-select-sm @error('size') is-invalid @enderror" id="size" name="size">
                            <option value="" disabled {{ old('size') ? '' : 'selected' }}>Select size</option>
                            <option value="1-10" {{ old('size') == '1-10' ? 'selected' : '' }}>1–10 employees</option>
                            <option value="11-50" {{ old('size') == '11-50' ? 'selected' : '' }}>11–50 employees</option>
                            <option value="51-200" {{ old('size') == '51-200' ? 'selected' : '' }}>51–200 employees</option>
                            <option value="201-500" {{ old('size') == '201-500' ? 'selected' : '' }}>201–500 employees</option>
                            <option value="500+" {{ old('size') == '500+' ? 'selected' : '' }}>500+ employees</option>
                        </select>
                        @error('size')<div class="invalid-feedback"><small>{{ $message }}</small></div>@enderror
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control form-control-sm" id="location" name="location" value="{{ old('location') }}" placeholder="e.g. Mumbai, India">
                    </div>
                    <div class="col-6">
                        <label for="sector" class="form-label">Sector</label>
                        <select class="form-select form-select-sm" id="sector" name="sector">
                            <option value="private" {{ old('sector', 'private') == 'private' ? 'selected' : '' }}>Private</option>
                            <option value="government" {{ old('sector') == 'government' ? 'selected' : '' }}>Government</option>
                            <option value="ngo" {{ old('sector') == 'ngo' ? 'selected' : '' }}>NGO / Non-profit</option>
                            <option value="other" {{ old('sector') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="website" class="form-label">Company Website <span class="text-muted small">(optional)</span></label>
                    <input type="url" class="form-control form-control-sm @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="https://yourcompany.com">
                    @error('website')<div class="invalid-feedback"><small>{{ $message }}</small></div>@enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    Register Company
                </button>
            </form>

            <div class="text-center pt-3 border-top">
                <span class="text-muted small">Already have an account?</span>
                <a href="/login" class="small fw-semibold ms-1">Sign In</a>
            </div>

            <div class="text-center mt-2">
                <a href="/register" class="text-muted small text-decoration-none">
                    <i class="bi bi-person me-1"></i> Looking for opportunities? Create a job seeker account &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
