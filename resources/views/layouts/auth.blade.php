<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication - InternTrack')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f8fafc;
        }
        .auth-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 480px;
            padding: 32px;
        }
        .form-control, .form-select {
            background-color: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            background-color: #020617;
            color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.25);
        }
        .btn-primary-gradient {
            background: linear-gradient(135deg, #6366f1, #a855f7);
            border: none;
            color: white;
            font-weight: 600;
        }
        .btn-primary-gradient:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="/" class="text-decoration-none">
                <h3 class="fw-bold text-white mb-1"><i class="bi bi-rocket-takeoff text-primary me-2"></i>InternTrack</h3>
            </a>
            <p class="text-muted small">Job & Application Tracking Portal</p>
        </div>

        @include('components.alert')

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
