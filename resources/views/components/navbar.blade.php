<nav class="navbar navbar-expand-lg navbar-app sticky-top">
    <div class="container-fluid px-3 px-md-4">
        <a class="d-flex align-items-center text-decoration-none" href="/">
            <span class="fw-bold text-dark" style="letter-spacing: -0.02em; font-size: 1rem;">InternTrack</span>
        </a>

        <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-4"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                <li class="nav-item">
                    <a class="nav-link px-2 py-1 text-secondary fw-medium small" href="/jobs">Browse Jobs</a>
                </li>
                @auth
                    @if(auth()->user()->isCompany())
                        <li class="nav-item">
                            <a class="nav-link px-2 py-1 text-secondary fw-medium small" href="/company/dashboard">Company Portal</a>
                        </li>
                    @elseif(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link px-2 py-1 text-secondary fw-medium small" href="/admin/dashboard">Admin Console</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a class="nav-link px-2 py-1 text-secondary fw-medium small" href="/company/register">Employers</a>
                    </li>
                @endauth
            </ul>

            <div class="d-flex align-items-center gap-2">
                @auth
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person"></i>
                            <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li class="px-3 py-1 text-muted small fw-semibold text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">Account</li>
                            @if(auth()->user()->isStudent())
                                <li><a class="dropdown-item" href="/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="/applications"><i class="bi bi-journal-check"></i> My Applications</a></li>
                                <li><a class="dropdown-item" href="/saved-jobs"><i class="bi bi-bookmark"></i> Saved Jobs</a></li>
                                <li><a class="dropdown-item" href="/interviews"><i class="bi bi-calendar-event"></i> Interviews</a></li>
                                <li><a class="dropdown-item" href="/profile"><i class="bi bi-person"></i> Profile</a></li>
                            @elseif(auth()->user()->isCompany())
                                <li><a class="dropdown-item" href="/company/dashboard"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="/company/jobs"><i class="bi bi-card-checklist"></i> Manage Jobs</a></li>
                                <li><a class="dropdown-item" href="/company/applications"><i class="bi bi-people"></i> Applicants</a></li>
                                <li><a class="dropdown-item" href="/company/verification"><i class="bi bi-shield-check"></i> Verification</a></li>
                            @elseif(auth()->user()->isAdmin())
                                <li><a class="dropdown-item" href="/admin/dashboard"><i class="bi bi-speedometer2"></i> Overview</a></li>
                                <li><a class="dropdown-item" href="/admin/verifications"><i class="bi bi-patch-check"></i> Approvals</a></li>
                                <li><a class="dropdown-item" href="/admin/jobs"><i class="bi bi-list-stars"></i> All Jobs</a></li>
                                <li><a class="dropdown-item" href="/admin/users"><i class="bi bi-people"></i> Users</a></li>
                                <li><a class="dropdown-item" href="/admin/logs"><i class="bi bi-journal-text"></i> Audit Logs</a></li>
                            @endif
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="/logout" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    @if(!request()->is('login', 'register', 'company/register'))
                        <a href="/login" class="btn btn-outline-secondary btn-sm">Log in</a>
                        <a href="/register" class="btn btn-primary btn-sm">Sign up</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>