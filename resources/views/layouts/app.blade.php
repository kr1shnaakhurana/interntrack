<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'InternTrack - Job & Application Tracking Platform')</title>
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
            --navbar-height: 64px;
        }
        body { font-family: 'Inter', system-ui, sans-serif; background-color: var(--bg-primary); color: var(--text-primary); }
        .btn-primary { background-color: var(--brand-primary); border-color: var(--brand-primary); }
        .btn-primary:hover, .btn-primary:focus-visible { background-color: var(--brand-primary-hover); border-color: var(--brand-primary-hover); color: #fff; }
        .btn-outline-primary { color: var(--brand-primary); border-color: var(--brand-primary); }
        .btn-outline-primary:hover, .btn-outline-primary:focus-visible { background-color: var(--brand-primary-light); border-color: var(--brand-primary); color: var(--brand-primary); }
        .text-primary { color: var(--brand-primary) !important; }
        .bg-primary-light { background-color: var(--brand-primary-light) !important; }
        .form-control:focus, .form-select:focus { border-color: var(--brand-primary); box-shadow: 0 0 0 0.2rem var(--brand-primary-light); }
        .form-check-input { border-color: #cbd5e1; }
        .form-check-input:checked { background-color: var(--brand-primary); border-color: var(--brand-primary); }
        .form-check-input:focus, .form-check-input:focus-visible { border-color: var(--brand-primary); box-shadow: 0 0 0 0.2rem var(--brand-primary-light); }
        .form-check-input:focus:not(:focus-visible) { box-shadow: none; }
        .card { background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); border-radius: var(--radius); }
        .card-footer { background: transparent; border-top: 1px solid var(--border-color); }
        .table-light { background-color: #f8fafc; }
        .table > :not(caption) > * > * { border-bottom-color: var(--border-color); }
        .navbar-brand { font-weight: 700; font-size: 1.25rem; color: var(--brand-primary) !important; }
        .brand-wordmark { font-weight: 800; font-size: 1.5rem; color: #111827; letter-spacing: -0.02em; line-height: 1.1; }
        .navbar { min-height: var(--navbar-height); }
        .sidebar-brand {
            min-height: 64px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -1rem -1rem 1rem;
            padding: 0 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .min-w-0 { min-width: 0; }
        .text-success { color: #059669 !important; }
        .text-warning { color: #b45309 !important; }
        .text-info { color: #0369a1 !important; }
        .bg-success-light { background-color: #ecfdf5 !important; }
        .navbar-light .nav-link { color: var(--text-secondary) !important; font-weight: 500; }
        .navbar-light .nav-link:hover { color: var(--brand-primary) !important; }
        .sidebar-link { color: var(--text-secondary); font-weight: 500; transition: color 0.15s ease, background-color 0.15s ease; }
        .sidebar-link:hover, .sidebar-link.active { background-color: var(--brand-primary-light); color: var(--brand-primary) !important; }
        .badge { font-weight: 500; }
        .page-header { border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .page-title { font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin: 0; }
        .sticky-20 { top: 20px; }
        .sticky-100 { top: 100px; }
        .stat-card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .job-card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .job-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }

        /* Blade components */
        .job-card-title { color: #111827; letter-spacing: -0.01em; }
        .job-card-company { font-size: 0.875rem; font-weight: 500; color: #6b7280; }
        .job-card-meta { font-size: 0.8125rem; color: #6b7280; }
        .job-card-pay { font-size: 0.875rem; color: #059669; }
        .job-card-verified { background-color: #eef2ff; color: #283f8f; font-size: 0.7rem; font-weight: 500; }
        .job-card-remote { background-color: #ecfdf5; color: #059669; font-size: 0.7rem; font-weight: 500; }
        .job-card-source { background-color: #283f8f; color: #fff; font-size: 0.7rem; font-weight: 500; }
        .job-card-source-alt { background-color: #fff; color: #4b5563; font-size: 0.7rem; font-weight: 500; }
        .job-card-cta { font-size: 0.8125rem; font-weight: 500; padding: 0.4rem 1.25rem; }
        .empty-state-icon { font-size: 3rem; }
        .sidebar { border-right: 1px solid var(--border-color); }
        
        /* Fixed sidebar layout - NO horizontal scroll */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            z-index: 1030;
            background: var(--bg-card);
            border-right: 1px solid var(--border-color);
        }
        
        .main-content-area {
            margin-left: 0;
            min-height: calc(100vh - var(--navbar-height));
            flex: 1 1 0;
            min-width: 0;
            transition: margin-left 0.3s ease;
        }
        
        .main-content-area.with-sidebar {
            margin-left: var(--sidebar-width);
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

        @media (max-width: 991.98px) {
            .sidebar {
                left: -100%;
                width: 280px;
                box-shadow: var(--shadow-md);
            }
            .sidebar.show {
                left: 0;
            }
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1030;
                display: none;
            }
            .sidebar-overlay.show {
                display: block;
            }
            .main-content-area.with-sidebar {
                margin-left: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('components.navbar')

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @if(auth()->check() && !request()->is('login', 'register', 'logout', 'jobs', 'jobs/*'))
                <div class="sidebar-overlay" id="sidebarOverlay"></div>
                @include('components.sidebar')
            @endif

            <main class="main-content-area @if(auth()->check() && !request()->is('login', 'register', 'logout', 'jobs', 'jobs/*')) with-sidebar @endif px-4 px-md-5 py-4">
                @include('components.alert')

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for mobile
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        
        if (toggleBtn && sidebar && overlay) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        // Active link highlighting
        document.querySelectorAll('.sidebar-link').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
        });

        // Auto-hide flash messages, pausing while the user is reading them
        document.addEventListener('DOMContentLoaded', function() {
            const autoHide = [
                { selector: '.alert.alert-success', delay: 3500 },
                { selector: '.alert.alert-warning, .alert.alert-info', delay: 3000 }
            ];

            autoHide.forEach(function (rule) {
                document.querySelectorAll(rule.selector).forEach(function (alert) {
                    if (!alert.classList.contains('show')) return;

                    const close = function () {
                        const instance = bootstrap.Alert.getOrCreateInstance(alert);
                        if (instance) instance.close();
                    };

                    let timer = setTimeout(close, rule.delay);

                    alert.addEventListener('mouseenter', function () {
                        clearTimeout(timer);
                    });

                    alert.addEventListener('mouseleave', function () {
                        timer = setTimeout(close, 1500);
                    });
                });
            });
        });
    </script>
</body>
</html>