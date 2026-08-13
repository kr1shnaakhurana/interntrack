<div class="sidebar p-3">
    <div class="mb-4 text-center pb-3 border-bottom border-secondary">
        <a href="/" class="text-decoration-none d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-rocket-takeoff text-primary fs-4"></i>
            <span class="fw-bold fs-5 text-white">InternTrack</span>
        </a>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        @if(auth()->user()->isStudent())
            <li class="nav-item mb-1">
                <a href="/dashboard" class="nav-link text-white {{ request()->is('dashboard') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/jobs" class="nav-link text-white {{ request()->is('jobs*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-briefcase me-2"></i> Browse Jobs
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/applications" class="nav-link text-white {{ request()->is('applications*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-journal-check me-2"></i> My Applications
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/interviews" class="nav-link text-white {{ request()->is('interviews*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-calendar-event me-2"></i> Interviews
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/saved-jobs" class="nav-link text-white {{ request()->is('saved-jobs*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-bookmark-heart me-2"></i> Saved Jobs
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/profile" class="nav-link text-white {{ request()->is('profile*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-person-badge me-2"></i> Student Profile
                </a>
            </li>
        @elseif(auth()->user()->isCompany())
            <li class="nav-item mb-1">
                <a href="/company/dashboard" class="nav-link text-white {{ request()->is('company/dashboard') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Company Dashboard
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/company/jobs" class="nav-link text-white {{ request()->is('company/jobs') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-card-checklist me-2"></i> Manage Job Postings
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/company/jobs/create" class="nav-link text-white {{ request()->is('company/jobs/create') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-plus-circle me-2"></i> Post New Job
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/company/applications" class="nav-link text-white {{ request()->is('company/applications*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-people me-2"></i> Applicants
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/company/verification" class="nav-link text-white {{ request()->is('company/verification*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-shield-check me-2"></i> Org Verification
                </a>
            </li>
        @elseif(auth()->user()->isAdmin())
            <li class="nav-item mb-1">
                <a href="/admin/dashboard" class="nav-link text-white {{ request()->is('admin/dashboard') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Admin Overview
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/admin/verifications" class="nav-link text-white {{ request()->is('admin/verifications*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-patch-check me-2"></i> Company Approvals
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/admin/companies" class="nav-link text-white {{ request()->is('admin/companies*') ? 'active bg-primary' : '' }}">
                    <i class="bi bg-building me-2"></i> All Companies
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/admin/jobs" class="nav-link text-white {{ request()->is('admin/jobs*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-list-stars me-2"></i> All Job Listings
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/admin/users" class="nav-link text-white {{ request()->is('admin/users*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-people-fill me-2"></i> User Directory
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="/admin/logs" class="nav-link text-white {{ request()->is('admin/logs*') ? 'active bg-primary' : '' }}">
                    <i class="bi bi-journal-text me-2"></i> Admin Audit Logs
                </a>
            </li>
        @endif
    </ul>

    <div class="mt-auto pt-3 border-top border-secondary">
        <form action="/logout" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                <i class="bi bi-box-arrow-right me-1"></i> Sign Out
            </button>
        </form>
    </div>
</div>
