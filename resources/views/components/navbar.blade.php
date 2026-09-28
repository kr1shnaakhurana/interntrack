<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom border-1 border-gray-200 sticky-top px-3 px-md-4 py-2">
    <div class="container-fluid">
        <a class="brand-wordmark text-decoration-none" href="/">InternTrack</a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                @auth
                    @if(auth()->user()->isCompany())
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded" href="/company/dashboard">Company Portal</a>
                        </li>
                    @elseif(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link px-3 py-2 rounded" href="/admin/dashboard">Admin Console</a>
                        </li>
                    @endif
@else
                    @if(request()->routeIs('home'))
                    <li class="nav-item">
                        <a class="nav-link px-3 py-2 rounded" href="/company/login">For companies</a>
                    </li>
                    @endif
                @endauth
            </ul>

            <div class="d-flex align-items-center gap-2">
                @auth
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle px-3 py-2 rounded d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-none d-md-inline fw-medium">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                            <li><h6 class="dropdown-header">Account</h6></li>
                            @if(auth()->user()->isStudent())
                                <li><a class="dropdown-item" href="/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item" href="/applications"><i class="bi bi-journal-check me-2"></i>Applications</a></li>
                                <li><a class="dropdown-item" href="/saved-jobs"><i class="bi bi-bookmark-heart me-2"></i>Saved Jobs</a></li>
                                <li><a class="dropdown-item" href="/interviews"><i class="bi bi-calendar-event me-2"></i>Interviews</a></li>
                                <li><a class="dropdown-item" href="/profile"><i class="bi bi-person-badge me-2"></i>Profile</a></li>
                            @elseif(auth()->user()->isCompany())
                                <li><a class="dropdown-item" href="/company/dashboard"><i class="bi bi-speedometer2 me-2"></i>Company Dashboard</a></li>
                                <li><a class="dropdown-item" href="/company/jobs"><i class="bi bi-card-checklist me-2"></i>Job Postings</a></li>
                                <li><a class="dropdown-item" href="/company/applications"><i class="bi bi-people me-2"></i>Applicants</a></li>
                                <li><a class="dropdown-item" href="/company/verification"><i class="bi bi-shield-check me-2"></i>Verification</a></li>
                            @elseif(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="/admin/dashboard"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                                <li><a class="dropdown-item" href="/admin/verifications"><i class="bi bi-patch-check me-2"></i>Company Approvals</a></li>
                                <li><a class="dropdown-item" href="/admin/jobs"><i class="bi bi-list-stars me-2"></i>All Jobs</a></li>
                                <li><a class="dropdown-item" href="/admin/users"><i class="bi bi-people-fill me-2"></i>Users</a></li>
                                <li><a class="dropdown-item" href="/admin/logs"><i class="bi bi-journal-text me-2"></i>Audit Logs</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="/logout" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    @if(!request()->is('login', 'register'))
                        <a href="/login" class="btn btn-outline-primary btn-sm rounded-pill px-4">Log in</a>
                        <a href="/register" class="btn btn-primary btn-sm rounded-pill px-4">Sign up</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>