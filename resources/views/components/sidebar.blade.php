<aside class="sidebar d-none d-lg-block vh-100 p-3">
    <div class="h-100 d-flex flex-column">
        <div class="sidebar-brand">
            <a href="/" class="brand-wordmark text-decoration-none d-block">InternTrack</a>
        </div>

        <nav class="flex-grow-1">
            <ul class="list-unstyled space-y-1">
                @if(auth()->user()->isStudent())
                    <li>
                        <a href="/dashboard" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-speedometer2 fs-5"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/jobs" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-briefcase fs-5"></i> <span>Browse Jobs</span>
                        </a>
                    </li>
                    <li>
                        <a href="/applications" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-journal-check fs-5"></i> <span>My Applications</span>
                        </a>
                    </li>
                    <li>
                        <a href="/interviews" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-calendar-event fs-5"></i> <span>Interviews</span>
                        </a>
                    </li>
                    <li>
                        <a href="/saved-jobs" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-bookmark-heart fs-5"></i> <span>Saved Jobs</span>
                        </a>
                    </li>
                    <li>
                        <a href="/profile" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-person-badge fs-5"></i> <span>Profile</span>
                        </a>
                    </li>

                @elseif(auth()->user()->isCompany())
                    <li>
                        <a href="/company/dashboard" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-speedometer2 fs-5"></i> <span>Company Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/company/jobs" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-card-checklist fs-5"></i> <span>Manage Job Postings</span>
                        </a>
                    </li>
                    <li>
                        <a href="/company/jobs/create" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-plus-circle fs-5"></i> <span>Post New Job</span>
                        </a>
                    </li>
                    <li>
                        <a href="/company/applications" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-people fs-5"></i> <span>Applicants</span>
                        </a>
                    </li>
                    <li>
                        <a href="/company/verification" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-shield-check fs-5"></i> <span>Company Verification</span>
                        </a>
                    </li>

                @elseif(auth()->user()->isAdmin())
                    <li>
                        <a href="/admin/dashboard" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-speedometer2 fs-5"></i> <span>Admin Overview</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/verifications" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-patch-check fs-5"></i> <span>Company Approvals</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/companies" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-building fs-5"></i> <span>All Companies</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/jobs" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-list-stars fs-5"></i> <span>All Job Listings</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/users" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-people-fill fs-5"></i> <span>User Directory</span>
                        </a>
                    </li>
                    <li>
                        <a href="/admin/logs" class="sidebar-link d-flex align-items-center gap-3 rounded px-3 py-2">
                            <i class="bi bi-journal-text fs-5"></i> <span>Admin Audit Logs</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>