<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternTrack - Career Discovery &amp; Internship Management Platform</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/interntrack.css" rel="stylesheet">

    <style>
        .hero-banner {
            border-bottom: 1px solid var(--it-border);
            background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
            padding: 64px 0 56px;
        }
        .hero-badge {
            font-size: 11.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--it-primary);
            background-color: var(--it-primary-subtle);
            border: 1px solid var(--it-primary-border);
            padding: 3px 10px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .feature-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 4px;
            background-color: var(--it-primary-subtle);
            color: var(--it-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-white">

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-app sticky-top">
        <div class="container-xl px-3 px-md-4">
            <a class="d-flex align-items-center gap-2 text-decoration-none" href="/">
                <span class="fw-bold text-dark fs-6" style="letter-spacing: -0.02em;">InternTrack</span>
            </a>

            <button class="navbar-toggler border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#welcomeNav" aria-controls="welcomeNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="welcomeNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4 gap-1">
                    <li class="nav-item">
                        <a class="nav-link px-2 py-1 text-secondary fw-medium small" href="/jobs">Browse Opportunities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-2 py-1 text-secondary fw-medium small" href="/company/register">For Employers</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        @if(auth()->user()->isStudent())
                            <a href="/dashboard" class="btn btn-primary btn-sm">
                                <i class="bi bi-speedometer2"></i> Candidate Dashboard
                            </a>
                        @elseif(auth()->user()->isCompany())
                            <a href="/company/dashboard" class="btn btn-primary btn-sm">
                                <i class="bi bi-speedometer2"></i> Company Portal
                            </a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="/admin/dashboard" class="btn btn-primary btn-sm">
                                <i class="bi bi-speedometer2"></i> Admin Console
                            </a>
                        @endif
                    @else
                        <a href="/login" class="btn btn-outline-secondary btn-sm">Sign In</a>
                        <a href="/register" class="btn btn-primary btn-sm">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-banner">
        <div class="container-xl px-3 px-md-4">
            <div class="row justify-content-center text-center">
                <div class="col-12 col-lg-9 col-xl-8">
                    <div class="mb-3">
                        <span class="hero-badge">
                            <i class="bi bi-patch-check-fill"></i> Verified Career Infrastructure
                        </span>
                    </div>

                    <h1 class="fw-bold text-dark mb-3" style="font-size: clamp(1.8rem, 3.5vw, 2.6rem); letter-spacing: -0.025em; line-height: 1.2;">
                        The Professional Platform for Internship Discovery &amp; Candidate Tracking
                    </h1>

                    <p class="text-secondary mb-4 mx-auto" style="max-width: 620px; font-size: 14.5px; line-height: 1.6;">
                        A centralized system built for students, university career centers, and vetted employers. Discover opportunities, manage applications through visual timelines, and streamline recruitment pipelines.
                    </p>

                    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                        <a href="/jobs" class="btn btn-primary btn-lg">
                            <i class="bi bi-search"></i> Explore Opportunities
                        </a>
                        <a href="/company/register" class="btn btn-outline-secondary btn-lg">
                            <i class="bi bi-building"></i> Employer Portal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Platform Architecture & Audience Pillars -->
    <section class="py-5 bg-white border-bottom">
        <div class="container-xl px-3 px-md-4">
            <div class="row g-4">
                <!-- Students Card -->
                <div class="col-12 col-lg-6">
                    <div class="card h-100 p-2">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="feature-icon-box">
                                    <i class="bi bi-mortarboard"></i>
                                </div>
                                <div>
                                    <h2 class="fs-6 fw-semibold text-dark mb-0">For University Candidates</h2>
                                    <span class="text-muted small">Comprehensive job discovery &amp; application management</span>
                                </div>
                            </div>

                            <p class="text-secondary small mb-3">
                                Seamlessly discover verified direct employer roles and external opportunities. Upload and maintain multiple resumes, submit applications with a single click, and follow your application journey with milestone timelines.
                            </p>

                            <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-4">
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check2 text-primary mt-0.5"></i>
                                    <span>Verified direct postings and aggregated feeds from reputable sources</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check2 text-primary mt-0.5"></i>
                                    <span>Personalized Resume Vault with versioning and default pickers</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check2 text-primary mt-0.5"></i>
                                    <span>Transparent interview schedule tracking with assessment meeting links</span>
                                </li>
                            </ul>

                            <div class="mt-auto pt-2">
                                <a href="/register" class="btn btn-outline-primary btn-sm">Create Candidate Account &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employers Card -->
                <div class="col-12 col-lg-6">
                    <div class="card h-100 p-2">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="feature-icon-box">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div>
                                    <h2 class="fs-6 fw-semibold text-dark mb-0">For Hiring Organizations</h2>
                                    <span class="text-muted small">Verified employer status &amp; applicant management</span>
                                </div>
                            </div>

                            <p class="text-secondary small mb-3">
                                Gain trust through official administrative verification. Publish customized opportunity listings, review qualified student applicants, and advance candidates through structured interview rounds.
                            </p>

                            <ul class="list-unstyled small text-secondary d-flex flex-column gap-2 mb-4">
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check2 text-primary mt-0.5"></i>
                                    <span>Document-backed employer verification for trusted hiring credentials</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check2 text-primary mt-0.5"></i>
                                    <span>Integrated applicant tracking with resume downloads and candidate statuses</span>
                                </li>
                                <li class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check2 text-primary mt-0.5"></i>
                                    <span>Multi-stage interview scheduling and direct candidate communication</span>
                                </li>
                            </ul>

                            <div class="mt-auto pt-2">
                                <a href="/company/register" class="btn btn-outline-secondary btn-sm">Employer Portal &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Opportunities Section -->
    <section class="py-5" style="background-color: var(--it-bg);">
        <div class="container-xl px-3 px-md-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
                <div>
                    <h2 class="page-title fs-5 mb-0">Recent Postings</h2>
                    <p class="text-muted small mb-0">Latest opportunities submitted by verified companies and academic partners.</p>
                </div>
                <a href="/jobs" class="btn btn-outline-secondary btn-sm">
                    View All {{ \App\Models\Job::count() }} Opportunities &rarr;
                </a>
            </div>

            <div class="row g-3">
                @forelse($recentJobs as $job)
                    <div class="col-12 col-md-6 col-lg-4">
                        <x-job-card :job="$job" />
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card p-4">
                            <x-empty-state title="No Active Postings" message="Check back shortly as new roles are added daily." icon="bi-briefcase" />
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Featured Verified Employers -->
    @if(isset($featuredCompanies) && $featuredCompanies->count() > 0)
        <section class="py-4 bg-white border-top border-bottom">
            <div class="container-xl px-3 px-md-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px; letter-spacing: 0.05em;">Verified Partners &amp; Employers</span>
                    <a href="/jobs" class="text-muted small text-decoration-none">Explore All &rarr;</a>
                </div>
                <div class="row g-3">
                    @foreach($featuredCompanies as $comp)
                        <div class="col-6 col-md-3">
                            <div class="border rounded-1 p-3 bg-light bg-opacity-50 h-100">
                                <div class="fw-semibold text-dark small text-truncate">{{ $comp->name }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $comp->sector ? ucfirst($comp->sector) : 'Enterprise' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Footer -->
    <footer class="py-4 bg-white">
        <div class="container-xl px-3 px-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark small">InternTrack</span>
                    <span class="text-muted small">&mdash; Career &amp; Application Management Platform</span>
                </div>
                <div class="d-flex align-items-center gap-3 text-muted small" style="font-size: 12px;">
                    <a href="/jobs" class="text-muted text-decoration-none">Opportunities</a>
                    <a href="/company/register" class="text-muted text-decoration-none">Employers</a>
                    <a href="/login" class="text-muted text-decoration-none">Sign In</a>
                    <span>&copy; {{ date('Y') }} InternTrack. All rights reserved.</span>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>