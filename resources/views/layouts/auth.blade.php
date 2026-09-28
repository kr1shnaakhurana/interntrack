<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Authentication - InternTrack')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body class="pt-5 pb-5 bg-gray-900 min-h-screen d-flex align-items-start justify-content-center" style="padding-top: 100px !important;">

    <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">

        <div class="text-center mb-4">
            <a href="/" class="underline-offset-4 underline">
                <h3 class="fw-bold text-white mb-1" style="font-size: 1.5rem; letter-spacing: -0.02em;">InternTrack</h3>
            </a>
            <p class="text-muted small">Job & Application Tracking Portal</p>
        </div>

        @include('components.alert')

        @yield('content')

    </div>

</body>
</html>