<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InternTrack - The smartest way to discover internships and hire early-career talent</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --navy: #172033;
            --navy-light: #1e293b;
            --mint: #EEF8F2;
            --mint-border: #d1fae5;
            --mint-text: #287A5F;
            --rich-blue: #283F8F;
            --rich-blue-hover: #1e40af;
            --rich-emerald: #287A5F;
            --rich-emerald-hover: #1e6b51;
            --company-gradient: linear-gradient(135deg, #172033 0%, #1e293b 50%, #283F8F 100%);
            --slate: #111827;
            --slate-muted: #4B5563;
        }
        body { font-family: 'Inter', system-ui, sans-serif; }

        .btn-navy { background: #111827; border-color: #111827; color: #fff; }
        .btn-navy:hover, .btn-navy:focus { background: #0f172a; border-color: #0f172a; color: #fff; }
        .btn-emerald { background: #287A5F; border-color: #287A5F; color: #fff; }
        .btn-emerald:hover, .btn-emerald:focus { background: #1e6b51; border-color: #1e6b51; color: #fff; }

        .text-rich-blue { color: #283F8F !important; }
        .text-rich-emerald { color: #287A5F !important; }
        .text-primary { color: #283F8F !important; }
        .text-success { color: #059669 !important; }
        .text-slate { color: var(--slate); }
        .text-slate-muted { color: var(--slate-muted); }

        .card-company { background: linear-gradient(135deg, #172033 0%, #1e293b 50%, #283F8F 100%); border: none; }
        .card-candidate { background: #EEF8F2; border: 1px solid #d1fae5; }
        .card-horizontal { min-height: 460px; }
        .card-lead { font-size: 1.05rem; line-height: 1.6; color: #1F2937; }
        .card-lead-light { font-size: 1.05rem; line-height: 1.6; color: #F3F4F6; }
        .card-icon-light { color: #E5E7EB; }

        .hero-headline { font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 800; line-height: 1.2; letter-spacing: -0.02em; }
        .hero-section { padding-top: 90px; }
        .section-title { font-size: clamp(1.5rem, 3vw, 2rem); }
        .letter-tight { letter-spacing: -0.01em; }
        .section-intro { font-size: 1.1rem; max-width: 700px; margin: 0 auto; color: var(--slate-muted); white-space: nowrap; }
        .section-lead { font-size: 1rem; max-width: 540px; margin: 0 auto; color: #6B7280; }
        .footer-copy { line-height: 1.7; color: var(--slate-muted); }

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

        .navbar-brand { font-weight: 800; font-size: 1.5rem; color: #111827 !important; }
        .nav-link-custom { color: #4B5563 !important; font-weight: 500; }
        .nav-link-custom:hover { color: #172033 !important; }

        .home-recent-section {
            padding-top: 48px !important;
        }

        @media (min-width: 992px) {
            .card-horizontal .card-body { display: flex; flex-direction: row; align-items: center; }
            .card-horizontal .icon-wrapper { flex-shrink: 0; margin-right: 2rem; }
            .card-horizontal .content-wrapper { flex: 1; text-align: left; }
            .card-horizontal .btn { align-self: flex-start; }
        }
    </style>
</head>
<body class="bg-white">

<nav class="navbar navbar-expand-lg fixed-top py-3 px-4 bg-white border-bottom border-gray-100">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="/">InternTrack</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom px-3 py-2" href="/jobs">For job seekers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom px-3 py-2" href="/company/login">For companies</a>
                </li>
                <li class="nav-item ms-2">
                    <a class="nav-link nav-link-custom px-3 py-2" href="/login">Log in</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-navy rounded-pill px-4 py-2 fw-semibold" href="/register">Sign up</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero-section d-flex align-items-center pb-6">
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-11 col-xl-10 text-center">
                <h1 class="hero-headline text-slate mb-4">
                    The <span class="text-rich-blue">smartest way</span> to discover <span class="text-rich-emerald">internships</span> and <span class="text-rich-emerald">hire early-career talent</span>
                </h1>

                <div class="row g-5 mb-4">
                    <div class="col-12 col-lg-6">
                        <div class="card card-company card-horizontal h-100 rounded-5 text-white">
                            <div class="card-body d-flex flex-column h-100 p-5">
                                <div class="icon-wrapper mb-3">
                                    <i class="bi bi-people-fill fs-1 card-icon-light"></i>
                                </div>
                                <div class="content-wrapper flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <h2 class="section-title text-white fw-bold mb-2">For Companies</h2>
                                        <p class="card-lead-light">
                                            Post verified roles, manage applicants, and build your employer brand. Join hundreds of companies hiring early-career talent on InternTrack.
                                        </p>
                                    </div>
                                    <a href="/company/login" class="btn btn-navy rounded-pill px-5 py-3 fw-semibold mt-3 align-self-start">Start hiring &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card card-candidate card-horizontal h-100 rounded-5">
                            <div class="card-body d-flex flex-column h-100 p-5">
                                <div class="icon-wrapper mb-3">
                                    <i class="bi bi-briefcase-fill fs-1 text-rich-emerald"></i>
                                </div>
                                <div class="content-wrapper flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <h2 class="section-title text-slate fw-bold mb-2">For Candidates</h2>
                                        <p class="card-lead">
                                            Discover internships, fellowships, and full-time roles from verified employers. Track every application in one place.
                                        </p>
                                    </div>
                                    <a href="/jobs" class="btn btn-emerald rounded-pill px-5 py-3 fw-semibold mt-3 align-self-start">Browse jobs &rarr;</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="home-features-section py-8 bg-gray-50 pb-8">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title text-slate fw-bold mb-3">Why InternTrack?</h2>
            <p class="section-intro mb-4">Everything you need to discover opportunities and manage your applications.</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="bg-white rounded-4 p-4 h-100 border border-gray-100">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-inline-flex mb-3">
                        <i class="bi bi-compass fs-4"></i>
                    </div>
                    <h5 class="text-slate fw-bold mb-2">Multi-Source Discovery</h5>
                    <p class="text-slate-muted small mb-0">Browse direct employer postings alongside aggregated external opportunities.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="bg-white rounded-4 p-4 h-100 border border-gray-100">
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 d-inline-flex mb-3">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <h5 class="text-slate fw-bold mb-2">Verified Employers</h5>
                    <p class="text-slate-muted small mb-0">Transparent verification badges ensure legitimate organization opportunities.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="bg-white rounded-4 p-4 h-100 border border-gray-100">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 d-inline-flex mb-3">
                        <i class="bi bi-kanban fs-4"></i>
                    </div>
                    <h5 class="text-slate fw-bold mb-2">Application Tracker</h5>
                    <p class="text-slate-muted small mb-0">Visual timelines tracking your status from applied to offer selection.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="bg-white rounded-4 p-4 h-100 border border-gray-100">
                    <div class="bg-purple bg-opacity-10 text-purple rounded-3 p-3 d-inline-flex mb-3">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                    <h5 class="text-slate fw-bold mb-2">Interview Rounds</h5>
                    <p class="text-slate-muted small mb-0">Manage technical, HR, and online interview rounds seamlessly.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="home-recent-section pt-12 pb-12">
    <div class="container">
        <div class="text-center mb-6">
            <h2 class="section-title letter-tight text-slate fw-semibold mb-3">Recent Opportunities</h2>
            <p class="section-lead">Explore verified internships, fellowships, and early-career roles.</p>
        </div>

        <div class="row g-4">
            @forelse($recentJobs as $job)
                <div class="col-12 col-md-6 col-lg-4">
                    <x-job-card :job="$job" />
                </div>
            @empty
                <div class="col-12">
                    <x-empty-state title="No Jobs Listed Yet" message="Run the database seeder or check back later." />
                </div>
            @endforelse
        </div>

        <div class="text-center my-5">
            <a href="/jobs" class="btn btn-outline-primary btn-lg rounded-pill px-5">View all opportunities</a>
        </div>
    </div>
</section>

<footer class="bg-gray-100 py-4">
    <div class="container">
        <div class="border-top border-gray-200 mb-4"></div>

        <div class="row g-5 align-items-center">
            <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                <h6 class="text-slate fw-bold mb-3">Top Job Sectors</h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-secondary border border-gray-200 px-3 py-2 small">Software Engineering</span>
                    <span class="badge bg-light text-secondary border border-gray-200 px-3 py-2 small">Design & UX</span>
                    <span class="badge bg-light text-secondary border border-gray-200 px-3 py-2 small">Marketing & Growth</span>
                    <span class="badge bg-light text-secondary border border-gray-200 px-3 py-2 small">Finance & Analytics</span>
                    <span class="badge bg-light text-secondary border border-gray-200 px-3 py-2 small">Data Science</span>
                    <span class="badge bg-light text-secondary border border-gray-200 px-3 py-2 small">Product Management</span>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="text-lg-end">
                    <h6 class="text-slate fw-bold mb-2">About InternTrack</h6>
                    <p class="footer-copy small mb-0">
                        InternTrack is a unified platform built to streamline the internship hunt and help companies connect with early-career talent. We aggregate verified opportunities from direct employers and trusted sources so you can focus on what matters — building your career or your team.
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>