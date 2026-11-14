<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InternTrack Portal')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/interntrack.css" rel="stylesheet">

    <style>
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            z-index: 1040;
            display: none;
        }
        .sidebar-overlay.show { display: block; }

        @media (max-width: 991.98px) {
            .portal-sidebar {
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }
            .portal-sidebar.show {
                transform: translateX(0);
                box-shadow: var(--it-shadow-md);
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="portal-sidebar" id="portalSidebar">
        <div class="sidebar-brand-box">
            <a href="/" class="d-flex align-items-center text-decoration-none">
                <span class="sidebar-brand-title">InternTrack</span>
            </a>
            <div class="ms-auto">
                @if(auth()->user()->isAdmin())
                    <span class="sidebar-role-badge">Admin</span>
                @else
                    <span class="sidebar-role-badge">Employer</span>
                @endif
            </div>
        </div>

        <nav class="flex-grow-1 overflow-y-auto">
            <ul class="sidebar-nav-list">
                @if(auth()->user()->isAdmin())
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
                @else
                    <li>
                        <a href="/company/dashboard" class="sidebar-link">
                            <i class="bi bi-speedometer2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/company/jobs" class="sidebar-link">
                            <i class="bi bi-card-checklist"></i>
                            <span>Manage Jobs</span>
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
                            <span>Company Verification</span>
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

    <div class="portal-main">
        <header class="portal-header sticky-top">
            <div class="d-flex align-items-center justify-content-between w-100 gap-3">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm d-lg-none" id="sidebarToggle" aria-label="Toggle navigation">
                        <i class="bi bi-list"></i>
                    </button>
                    @hasSection('page_title')
                        <h1 class="page-title text-truncate mb-0">@yield('page_title')</h1>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if(auth()->user()->isAdmin())
                        <span class="badge badge-subtle-primary">Admin Console</span>
                    @elseif(auth()->user()->company && auth()->user()->company->isVerified())
                        <span class="badge badge-subtle-success"><i class="bi bi-patch-check-fill"></i> Verified</span>
                    @else
                        <span class="badge badge-subtle-warning"><i class="bi bi-shield-exclamation"></i> Unverified</span>
                    @endif

                    <div class="vr mx-1 d-none d-sm-block text-secondary opacity-25"></div>

                    <a href="/jobs" class="btn btn-outline-secondary btn-sm d-none d-md-inline-flex">
                        <i class="bi bi-box-arrow-up-right"></i> View Portal
                    </a>

                    <form action="/logout" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-3 p-md-4">
            <div class="container-fluid p-0">
                @include('components.alert')

                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
    <script>
        (function () {
            const sidebar = document.getElementById('portalSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('sidebarToggle');

            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('show');
                if (overlay) overlay.classList.remove('show');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
            }

            if (toggle && sidebar && overlay) {
                toggle.addEventListener('click', function () {
                    const open = sidebar.classList.toggle('show');
                    overlay.classList.toggle('show', open);
                    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
                overlay.addEventListener('click', closeSidebar);
                window.addEventListener('resize', function () {
                    if (window.innerWidth >= 992) closeSidebar();
                });
            }

            document.querySelectorAll('.portal-sidebar .sidebar-link').forEach(function (link) {
                if (link.getAttribute('href') === window.location.pathname) {
                    link.classList.add('active');
                }
            });

            const autoHide = [
                { selector: '.alert.alert-success', delay: 4000 },
                { selector: '.alert.alert-warning, .alert.alert-info', delay: 3500 }
            ];

            autoHide.forEach(function (rule) {
                document.querySelectorAll(rule.selector).forEach(function (el) {
                    if (!el.classList.contains('alert-dismissible')) return;

                    const close = function () {
                        const instance = bootstrap.Alert.getOrCreateInstance(el);
                        if (instance) instance.close();
                    };

                    let timer = setTimeout(close, rule.delay);

                    el.addEventListener('mouseenter', function () {
                        clearTimeout(timer);
                    });

                    el.addEventListener('mouseleave', function () {
                        timer = setTimeout(close, 1500);
                    });
                });
            });
        })();
    </script>
</body>
</html>
