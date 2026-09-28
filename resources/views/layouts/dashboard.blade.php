<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Company Dashboard - InternTrack')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-primary: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --brand-primary: #4f46e5;
            --brand-primary-hover: #4338ca;
            --brand-primary-light: #eef2ff;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --radius: 0.75rem;
            --radius-lg: 1rem;
            --sidebar-width: 260px;
            --header-height: 64px;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .btn-primary { background-color: var(--brand-primary); border-color: var(--brand-primary); }
        .btn-primary:hover, .btn-primary:focus-visible { background-color: var(--brand-primary-hover); border-color: var(--brand-primary-hover); color: #fff; }
        .btn-outline-primary { color: var(--brand-primary); border-color: var(--brand-primary); }
        .btn-outline-primary:hover, .btn-outline-primary:focus-visible { background-color: var(--brand-primary-light); border-color: var(--brand-primary); color: var(--brand-primary); }
        .btn-outline-primary { color: var(--brand-primary); border-color: var(--brand-primary); }
        .btn-outline-primary:hover { background-color: var(--brand-primary-light); border-color: var(--brand-primary); color: var(--brand-primary); }
        .text-primary { color: var(--brand-primary) !important; }
        .bg-primary-light { background-color: var(--brand-primary-light) !important; }
        .clickable { cursor: pointer; }
        .cell-narrow { max-width: 200px; }
        .cell-medium { max-width: 250px; }
        .bg-success-light { background-color: #d1e7dd !important; }
        .bg-purple { background-color: #6f42c1 !important; color: #fff !important; }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 0.2rem var(--brand-primary-light);
        }
        .form-check-input { border-color: #cbd5e1; }
        .form-check-input:checked { background-color: var(--brand-primary); border-color: var(--brand-primary); }
        .form-check-input:focus, .form-check-input:focus-visible { border-color: var(--brand-primary); box-shadow: 0 0 0 0.2rem var(--brand-primary-light); }
        .form-check-input:focus:not(:focus-visible) { box-shadow: none; }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            border-radius: var(--radius);
        }
        .card-footer { background: transparent; border-top: 1px solid var(--border-color); }
        .table-light { background-color: #f8fafc; }
        .table > :not(caption) > * > * { border-bottom-color: var(--border-color); }
        .badge { font-weight: 500; }

        .table-responsive > table:not(.table) { width: 100%; border-collapse: collapse; }
        .table-responsive > table:not(.table) th,
        .table-responsive > table:not(.table) td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        .table-responsive > table:not(.table) thead th {
            background-color: #f8fafc;
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }
        .table-responsive > table:not(.table) tbody tr:hover { background-color: #f8fafc; }
        .table-responsive > table:not(.table) tbody tr:last-child td { border-bottom: 0; }

        .page-header {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .page-title { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0; }
        .stat-card, .job-card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .stat-card:hover, .job-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

        .portal-brand { line-height: 1.1; }
        .portal-brand-icon {
            width: 40px;
            height: 40px;
        }
        .portal-brand-row {
            min-height: var(--header-height);
            display: flex;
            align-items: center;
            margin: -1rem -1rem 1rem;
            padding: 0 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .min-w-0 { min-width: 0; }
        .portal-brand-name { font-size: 1.0625rem; }
        .portal-brand-sub {
            margin-top: 3px;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .portal-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            z-index: 1045;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
            overflow-y: auto;
        }
        .portal-sidebar .sidebar-link {
            color: var(--text-secondary);
            font-weight: 500;
            border-radius: 0.5rem;
            transition: color 0.15s ease, background-color 0.15s ease;
        }
        .portal-sidebar .sidebar-link:hover { background-color: var(--brand-primary-light); color: var(--brand-primary) !important; }
        .portal-sidebar .sidebar-link.active { background-color: var(--brand-primary-light); color: var(--brand-primary) !important; }

        .portal-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
        }

        .portal-header {
            height: var(--header-height);
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 1040;
            display: none;
        }
        .sidebar-overlay.show { display: block; }

        @media (max-width: 991.98px) {
            .portal-sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
            .portal-sidebar.show { transform: translateX(0); box-shadow: var(--shadow-md); }
            .portal-main { margin-left: 0; width: 100%; }
        }

        @keyframes alertSlideDown {
            from { opacity: 0; transform: translate3d(0, -10px, 0); }
            to { opacity: 1; transform: none; }
        }
        .alert-slide { animation: alertSlideDown 0.2s ease-out; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        .empty-state-icon { font-size: 3rem; }

        .bg-white { background-color: #fff !important; }
        .w-full { width: 100%; }
        .rounded-md { border-radius: 0.375rem !important; }
        .rounded-lg { border-radius: 0.5rem !important; }
        .rounded-xl { border-radius: 0.75rem !important; }
        .border-gray-200 { border-color: #e5e7eb !important; }
        .border-indigo-100 { border-color: #e0e7ff !important; }
        .bg-indigo-100 { background-color: #e0e7ff !important; }
        .text-indigo-800 { color: #3730a3 !important; }
        .text-gray-900 { color: #111827 !important; }
        .text-gray-500 { color: #6b7280 !important; }
        .text-gray-400 { color: #9ca3af !important; }
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .font-medium { font-weight: 500; }
        .flex { display: flex; }
        .inline-flex { display: inline-flex; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
    </style>

    @stack('styles')
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="portal-sidebar" id="portalSidebar">
        <div class="d-flex flex-column h-100 p-3">

            <div class="portal-brand-row d-flex align-items-center gap-2">
                @if(auth()->user()->isAdmin())
                    <span class="portal-brand-icon d-inline-flex align-items-center justify-content-center bg-primary-light text-primary rounded-3 flex-shrink-0">
                        <i class="bi bi-shield-lock fs-5"></i>
                    </span>
                    <div class="portal-brand text-truncate">
                        <div class="portal-brand-name fw-bold text-dark text-truncate">Admin Console</div>
                        <div class="portal-brand-sub text-truncate">InternTrack Admin</div>
                    </div>
                @else
                    <span class="portal-brand-icon d-inline-flex align-items-center justify-content-center bg-primary-light text-primary rounded-3 flex-shrink-0">
                        <i class="bi bi-building fs-5"></i>
                    </span>
                    <div class="portal-brand text-truncate">
                        <div class="portal-brand-name fw-bold text-dark text-truncate">{{ auth()->user()->company->name ?? 'Company Portal' }}</div>
                        <div class="portal-brand-sub text-truncate">Company Portal</div>
                    </div>
                @endif
            </div>

            <nav class="flex-grow-1">
                <ul class="list-unstyled d-flex flex-column gap-1 mb-0">
                    @if(auth()->user()->isAdmin())
                        <li>
                            <a href="/admin/dashboard" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-speedometer2"></i> <span>Admin Overview</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/verifications" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-patch-check"></i> <span>Company Approvals</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/companies" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-building"></i> <span>All Companies</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/jobs" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-list-stars"></i> <span>All Job Listings</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/users" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-people-fill"></i> <span>User Directory</span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/logs" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-journal-text"></i> <span>Admin Audit Logs</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="/company/dashboard" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/company/jobs" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-card-checklist"></i> <span>Manage Jobs</span>
                            </a>
                        </li>
                        <li>
                            <a href="/company/jobs/create" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-plus-circle"></i> <span>Post New Job</span>
                            </a>
                        </li>
                        <li>
                            <a href="/company/applications" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-people"></i> <span>Applicants</span>
                            </a>
                        </li>
                        <li>
                            <a href="/company/verification" class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 text-decoration-none text-nowrap">
                                <i class="bi bi-shield-check"></i> <span>Company Verification</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

        </div>
    </aside>

    <div class="portal-main">
        <header class="portal-header sticky-top">
            <div class="d-flex align-items-center justify-content-between gap-3 px-3 px-md-4 py-3">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <button type="button" class="btn btn-outline-secondary d-lg-none" id="sidebarToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="portalSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    @hasSection('page_title')
                        <h1 class="page-title text-truncate mb-0">@yield('page_title')</h1>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2 gap-md-3 mt-1 mt-md-2">
                    @if(auth()->user()->isAdmin())
                        <span class="badge rounded-pill bg-primary-light text-primary px-3 py-2">Admin</span>
                    @elseif(auth()->user()->company && auth()->user()->company->isVerified())
                        <span class="badge rounded-pill bg-success-light text-success px-3 py-2"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
                    @else
                        <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2"><i class="bi bi-shield-exclamation me-1"></i>Unverified</span>
                    @endif

                    <div class="d-none d-md-flex align-items-center gap-2 text-secondary min-w-0 pe-md-2 border-end">
                        <i class="bi bi-person-circle flex-shrink-0"></i>
                        <span class="small fw-medium text-truncate">{{ auth()->user()->name }}</span>
                    </div>

                    <form action="/logout" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 d-flex align-items-center gap-2">
                            <i class="bi bi-box-arrow-right"></i> <span class="d-none d-sm-inline">Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-3 p-md-4">
            @include('components.alert')

            @yield('content')
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
                { selector: '.alert.alert-success', delay: 3500 },
                { selector: '.alert.alert-warning, .alert.alert-info', delay: 3000 }
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
