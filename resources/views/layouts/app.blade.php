<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InternTrack - Internship & Career Management Platform')</title>
    
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
            z-index: 1025;
            display: none;
        }
        .sidebar-overlay.show { display: block; }

        @media (max-width: 991.98px) {
            .sidebar {
                left: -100%;
                width: 260px;
                transition: left 0.25s ease;
            }
            .sidebar.show {
                left: 0;
                display: flex !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('components.navbar')

    <div class="container-fluid p-0">
        <div class="d-flex">
            @if(auth()->check() && !request()->is('login', 'register', 'company/register', 'jobs', 'jobs/*'))
                <div class="sidebar-overlay" id="sidebarOverlay"></div>
                @include('components.sidebar')
            @endif

            <main class="main-content-area @if(auth()->check() && !request()->is('login', 'register', 'company/register', 'jobs', 'jobs/*')) with-sidebar @endif flex-grow-1 p-3 p-md-4">
                <div class="container-xl p-0">
                    @include('components.alert')

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Active link highlighting
        document.querySelectorAll('.sidebar-link').forEach(link => {
            if (link.getAttribute('href') === window.location.pathname) {
                link.classList.add('active');
            }
        });

        // Mobile sidebar toggle
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

        // Auto-dismissible flash messages
        document.addEventListener('DOMContentLoaded', function() {
            const autoHide = [
                { selector: '.alert.alert-success', delay: 4000 },
                { selector: '.alert.alert-warning, .alert.alert-info', delay: 3500 }
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