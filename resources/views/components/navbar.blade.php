<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3 border-bottom border-secondary">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="/">
            <i class="bi bi-rocket-takeoff text-primary"></i>
            <span class="navbar-brand-gradient">InternTrack</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('jobs*') ? 'active fw-bold' : '' }}" href="/jobs">
                        <i class="bi bi-briefcase me-1"></i> Explore Jobs
                    </a>
                </li>
                @auth
                    @if(auth()->user()->isStudent())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard') ? 'active fw-bold' : '' }}" href="/dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('applications*') ? 'active fw-bold' : '' }}" href="/applications">Applications</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('saved-jobs') ? 'active fw-bold' : '' }}" href="/saved-jobs">Saved Jobs</a>
                        </li>
                    @elseif(auth()->user()->isCompany())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('company/dashboard') ? 'active fw-bold' : '' }}" href="/company/dashboard">Company Portal</a>
                        </li>
                    @elseif(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/dashboard') ? 'active fw-bold' : '' }}" href="/admin/dashboard">Admin Console</a>
                        </li>
                    @endif
                @endauth
            </ul>

            <div class="d-flex align-items-center gap-2">
                @auth
                    <a href="/profile" class="btn btn-outline-light btn-sm px-3">
                        <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
                    </a>
                    <form action="/logout" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm px-3">Logout</button>
                    </form>
                @else
                    <a href="/login" class="btn btn-outline-light btn-sm px-3 me-2">Login</a>
                    <a href="/register" class="btn btn-primary btn-sm px-3 fw-semibold">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
