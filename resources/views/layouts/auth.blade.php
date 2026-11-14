<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication - InternTrack')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/interntrack.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-light min-vh-100 d-flex align-items-center justify-content-center py-4">

    <div class="card p-4 shadow-sm" style="width: 100%; max-width: 440px;">
        <div class="text-center mb-4">
            <a href="/" class="d-inline-flex align-items-center text-decoration-none mb-2">
                <span class="fw-bold text-dark fs-5">InternTrack</span>
            </a>
            <p class="text-muted small mb-0">Career Discovery &amp; Application Portal</p>
        </div>

        @include('components.alert')

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>