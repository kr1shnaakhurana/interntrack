@extends('layouts.auth')

@section('title', 'Register - InternTrack')

@section('content')
<form action="/register" method="POST">
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label text-light small fw-semibold">Full Name / Org Name</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required placeholder="John Doe">
    </div>

    <div class="mb-3">
        <label for="email" class="form-label text-light small fw-semibold">Email Address</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="name@domain.com">
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-6">
            <label for="password" class="form-label text-light small fw-semibold">Password</label>
            <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
        </div>
        <div class="col-md-6">
            <label for="password_confirmation" class="form-label text-light small fw-semibold">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="••••••••">
        </div>
    </div>

    <div class="mb-4">
        <label for="role" class="form-label text-light small fw-semibold">Account Role</label>
        <select class="form-select" id="role" name="role" required onchange="toggleRoleFields(this.value)">
            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>🎓 Student / Candidate</option>
            <option value="company" {{ old('role') == 'company' ? 'selected' : '' }}>🏢 Employer / Organization</option>
        </select>
        <small class="text-muted fs-8 d-block mt-1">* Note: Admin accounts cannot be created publicly.</small>
    </div>

    <div id="student-fields" class="mb-3">
        <div class="mb-2">
            <input type="text" class="form-control" name="program" placeholder="Program (e.g. B.Tech Computer Science)">
        </div>
    </div>

    <div id="company-fields" class="mb-3" style="display: none;">
        <div class="mb-2">
            <input type="text" class="form-control" name="company_name" placeholder="Official Company Name">
        </div>
    </div>

    <button type="submit" class="btn btn-primary-gradient w-100 py-2 mb-3">
        Create Account
    </button>

    <div class="text-center">
        <span class="text-muted small">Already have an account?</span>
        <a href="/login" class="text-primary text-decoration-none small fw-semibold ms-1">Sign In</a>
    </div>
</form>

<script>
    function toggleRoleFields(role) {
        document.getElementById('student-fields').style.display = role === 'student' ? 'block' : 'none';
        document.getElementById('company-fields').style.display = role === 'company' ? 'block' : 'none';
    }
</script>
@endsection
