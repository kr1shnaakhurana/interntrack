<aside class="sidebar d-none d-lg-flex flex-column vh-100">
    <div class="sidebar-brand-box">
        <a href="/" class="d-flex align-items-center text-decoration-none">
            <span class="sidebar-brand-title">InternTrack</span>
        </a>
        <div class="ms-auto">
            @if(auth()->user()->isAdmin())
                <span class="sidebar-role-badge">Admin</span>
            @elseif(auth()->user()->isCompany())
                <span class="sidebar-role-badge">Employer</span>
            @else
                <span class="sidebar-role-badge">Student</span>
            @endif
        </div>
    </div>

    <nav class="flex-grow-1 overflow-y-auto">
        <ul class="sidebar-nav-list">
            @if(auth()->user()->isStudent())
                <li>
                    <a href="/dashboard" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/jobs" class="sidebar-link">
                        <i class="bi bi-briefcase"></i>
                        <span>Browse Jobs</span>
                    </a>
                </li>
                <li>
                    <a href="/applications" class="sidebar-link">
                        <i class="bi bi-journal-check"></i>
                        <span>My Applications</span>
                    </a>
                </li>
                <li>
                    <a href="/interviews" class="sidebar-link">
                        <i class="bi bi-calendar-event"></i>
                        <span>Interviews</span>
                    </a>
                </li>
                <li>
                    <a href="/saved-jobs" class="sidebar-link">
                        <i class="bi bi-bookmark"></i>
                        <span>Saved Jobs</span>
                    </a>
                </li>
                <li>
                    <a href="/profile" class="sidebar-link">
                        <i class="bi bi-person"></i>
                        <span>Profile &amp; Resumes</span>
                    </a>
                </li>

            @elseif(auth()->user()->isCompany())
                <li>
                    <a href="/company/dashboard" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/company/jobs" class="sidebar-link">
                        <i class="bi bi-card-checklist"></i>
                        <span>Manage Postings</span>
                    </a>
                </li>
                <li>
                    <a href="/company/jobs/create" class="sidebar-link">
                        <i class="bi bi-plus-circle"></i>
                        <span>Post New Job</span>
                    </a>
                </li>
                <li>
                    <a href="/company/applications" class="sidebar-link">
                        <i class="bi bi-people"></i>
                        <span>Applicants</span>
                    </a>
                </li>
                <li>
                    <a href="/company/verification" class="sidebar-link">
                        <i class="bi bi-shield-check"></i>
                        <span>Verification</span>
                    </a>
                </li>

            @elseif(auth()->user()->isAdmin())
                <li>
                    <a href="/admin/dashboard" class="sidebar-link">
                        <i class="bi bi-speedometer2"></i>
                        <span>Admin Overview</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/verifications" class="sidebar-link">
                        <i class="bi bi-patch-check"></i>
                        <span>Company Approvals</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/companies" class="sidebar-link">
                        <i class="bi bi-building"></i>
                        <span>All Companies</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/jobs" class="sidebar-link">
                        <i class="bi bi-list-stars"></i>
                        <span>All Job Listings</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/job-sources" class="sidebar-link">
                        <i class="bi bi-cloud-arrow-down"></i>
                        <span>Job API Sources</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/users" class="sidebar-link">
                        <i class="bi bi-people"></i>
                        <span>User Directory</span>
                    </a>
                </li>
                <li>
                    <a href="/admin/logs" class="sidebar-link">
                        <i class="bi bi-journal-text"></i>
                        <span>Audit Logs</span>
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <div class="p-3 border-top bg-white">
        <div class="d-flex align-items-center justify-content-between">
            <div class="min-w-0">
                <div class="fw-semibold text-dark text-truncate small">{{ auth()->user()->name }}</div>
                <div class="text-muted small text-truncate" style="font-size: 11px;">{{ auth()->user()->email }}</div>
            </div>
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm p-1 px-2" title="Sign Out">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
</aside>