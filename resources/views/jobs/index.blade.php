@extends('layouts.app')

@section('title', 'Explore Opportunities - InternTrack')

@section('content')
    <div class="mb-3">
        <div
            class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h1 class="page-title">Opportunity Directory</h1>
                <p class="text-muted small mb-0">Discover verified direct employer openings, government recruitment drives,
                    research fellowships, and global remote opportunities.</p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <button type="button" id="btn-refresh-feed" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="bi bi-arrow-clockwise" id="refresh-icon"></i>
                    <span id="refresh-text">Refresh Feed</span>
                </button>
                <div class="text-muted small ps-2 border-start">
                    Showing <strong class="text-dark" id="showing-count">{{ $jobs->count() }}</strong> of <strong class="text-dark" id="total-count">{{ $jobs->total() }}</strong> opportunities
                </div>
            </div>
        </div>

        <!-- Feed Update Notification Banner -->
        <div id="feed-update-banner" class="alert alert-success d-none py-2 px-3 small align-items-center justify-content-between mb-3 shadow-sm" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-stars text-success"></i>
                <span id="feed-update-message">Feed refreshed with latest opportunities!</span>
            </div>
            <button type="button" class="btn-close btn-close-sm" onclick="this.parentElement.classList.add('d-none')"></button>
        </div>

        <!-- Filter Toolbar -->
        <div class="card mb-4">
            <div class="card-body p-3">
                <form action="/jobs" method="GET">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-3">
                            <label for="keyword" class="visually-hidden">Search</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i
                                        class="bi bi-search"></i></span>
                                <input type="text" name="keyword" id="keyword"
                                    class="form-control form-control-sm border-start-0 ps-0"
                                    placeholder="Title, employer, or skill..." value="{{ request('keyword') }}">
                            </div>
                        </div>

                        <div class="col-6 col-md-2">
                            <label for="job_type" class="visually-hidden">Job Type</label>
                            <select name="job_type" id="job_type" class="form-select form-select-sm">
                                <option value="">All Opportunity Types</option>
                                <option value="internship" {{ request('job_type') == 'internship' ? 'selected' : '' }}>
                                    Internship</option>
                                <option value="full-time" {{ request('job_type') == 'full-time' ? 'selected' : '' }}>Full-Time
                                </option>
                                <option value="part-time" {{ request('job_type') == 'part-time' ? 'selected' : '' }}>Part-Time
                                </option>
                                <option value="research" {{ request('job_type') == 'research' ? 'selected' : '' }}>Research
                                </option>
                                <option value="fellowship" {{ request('job_type') == 'fellowship' ? 'selected' : '' }}>
                                    Fellowship</option>
                                <option value="government" {{ request('job_type') == 'government' ? 'selected' : '' }}>
                                    Government</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <label for="sector" class="visually-hidden">Sector</label>
                            <select name="sector" id="sector" class="form-select form-select-sm">
                                <option value="">All Sectors</option>
                                <option value="private" {{ request('sector') == 'private' ? 'selected' : '' }}>Private
                                </option>
                                <option value="government" {{ request('sector') == 'government' ? 'selected' : '' }}>
                                    Government</option>
                                <option value="ngo" {{ request('sector') == 'ngo' ? 'selected' : '' }}>NGO</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-2">
                            <label for="government_category" class="visually-hidden">Govt Category</label>
                            <select name="government_category" id="government_category" class="form-select form-select-sm">
                                <option value="">All Govt Categories</option>
                                @foreach($govCategories as $gcat)
                                    <option value="{{ $gcat }}" {{ request('government_category') == $gcat ? 'selected' : '' }}>
                                        {{ $gcat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label for="location" class="visually-hidden">Location</label>
                            <input type="text" name="location" id="location" class="form-control form-control-sm"
                                placeholder="City or region..." value="{{ request('location') }}">
                        </div>
                    </div>

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 mt-2 border-top">
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" name="is_remote" value="1" id="is_remote" {{ request('is_remote') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="is_remote">Remote Only</label>
                            </div>
                            <div class="form-check m-0">
                                <input class="form-check-input" type="checkbox" name="verified_only" value="1"
                                    id="verified_only" {{ request('verified_only') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="verified_only">Verified Employers Only</label>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-muted small" style="font-size: 11px;">Source:</span>
                                <select name="source_type" class="form-select form-select-sm py-0"
                                    style="font-size: 12px; height: 26px;">
                                    <option value="">All Sources</option>
                                    <option value="direct" {{ request('source_type') == 'direct' ? 'selected' : '' }}>
                                        InternTrack Direct</option>
                                    <option value="external" {{ request('source_type') == 'external' ? 'selected' : '' }}>
                                        External Feeds</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="/jobs" class="btn btn-outline-secondary btn-sm">Reset</a>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-filter"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Job Cards Grid -->
        <div class="row g-3" id="jobs-grid">
            @forelse($jobs as $job)
                <div class="col-12 col-md-6 col-lg-4 job-item">
                    <x-job-card :job="$job" />
                </div>
            @empty
                <div class="col-12" id="no-jobs-card">
                    <div class="card p-4">
                        <x-empty-state title="No Opportunities Found"
                            message="Try adjusting or clearing your search filters to view more results." icon="bi-search">
                            <div class="mt-3">
                                <a href="/jobs" class="btn btn-outline-secondary btn-sm">Clear All Filters</a>
                            </div>
                        </x-empty-state>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Dynamic Loading Controls & Sentinel -->
        <div id="dynamic-loader-area" class="mt-4 mb-5 text-center">
            <!-- Loading Status Indicator -->
            <div id="loading-spinner" class="d-none py-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="text-muted small fw-medium">Loading opportunities dynamically...</span>
            </div>

            <!-- Manual Load More button as fallback / user control -->
            @if($jobs->hasMorePages())
                <div id="load-more-wrapper">
                    <button type="button" id="btn-load-more" class="btn btn-outline-primary btn-sm px-4">
                        <i class="bi bi-arrow-down-circle me-1"></i> Load More Opportunities
                    </button>
                </div>
            @endif

            <!-- Sentinel element for IntersectionObserver auto-loading -->
            <div id="scroll-sentinel" style="height: 12px;"></div>

            <!-- End of Opportunities Notice -->
            <div id="end-of-results" class="{{ $jobs->hasMorePages() ? 'd-none' : '' }} py-3 text-muted small">
                @if($jobs->total() > 0)
                    <div class="d-inline-flex align-items-center gap-1 text-secondary">
                        <i class="bi bi-check2-circle text-success fs-6"></i>
                        <span>You've loaded all available opportunities.</span>
                    </div>
                @endif
            </div>

            <!-- Error State with Retry -->
            <div id="loading-error" class="d-none py-2 text-danger small">
                <i class="bi bi-exclamation-circle me-1"></i>
                <span>Failed to load more opportunities.</span>
                <button type="button" id="btn-retry" class="btn btn-link btn-sm p-0 ms-1 text-primary">Retry</button>
            </div>
        </div>
    </div>

    <!-- Dynamic Loading Script & Unlimited Scroll Styles -->
    <style>
        @keyframes spinIcon {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .spin-animation {
            display: inline-block;
            animation: spinIcon 0.85s linear infinite;
        }
        .job-item {
            animation: fadeInUpItem 0.28s ease-out;
        }
        @keyframes fadeInUpItem {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let nextPageUrl = @json($jobs->nextPageUrl());
            let hasMore = true; // Unlimited scrolling enabled
            let isLoading = false;
            let currentlyLoadedCount = {{ $jobs->count() }};

            const grid = document.getElementById('jobs-grid');
            const spinner = document.getElementById('loading-spinner');
            const loadMoreWrapper = document.getElementById('load-more-wrapper');
            const loadMoreBtn = document.getElementById('btn-load-more');
            const endNotice = document.getElementById('end-of-results');
            const errorNotice = document.getElementById('loading-error');
            const retryBtn = document.getElementById('btn-retry');
            const sentinel = document.getElementById('scroll-sentinel');
            const showingCountEl = document.getElementById('showing-count');
            const totalCountEl = document.getElementById('total-count');
            const refreshBtn = document.getElementById('btn-refresh-feed');
            const refreshIcon = document.getElementById('refresh-icon');
            const refreshText = document.getElementById('refresh-text');
            const updateBanner = document.getElementById('feed-update-banner');

            function updateCounter(addedCount) {
                currentlyLoadedCount += addedCount;
                if (showingCountEl) {
                    showingCountEl.textContent = currentlyLoadedCount;
                }
            }

            // Unlimited Scroll & Intersection Check
            function checkScrollTrigger() {
                if (isLoading || !nextPageUrl) return;

                const scrollY = window.scrollY || window.pageYOffset;
                const windowHeight = window.innerHeight;
                const docHeight = Math.max(
                    document.body.scrollHeight, document.documentElement.scrollHeight,
                    document.body.offsetHeight, document.documentElement.offsetHeight
                );

                // Auto-fetch when user scrolls within 750px of bottom
                if (scrollY + windowHeight >= docHeight - 750) {
                    loadNextBatch();
                }
            }

            async function loadNextBatch() {
                if (isLoading || !nextPageUrl) {
                    return;
                }

                isLoading = true;
                if (spinner) spinner.classList.remove('d-none');
                if (loadMoreWrapper) loadMoreWrapper.classList.add('d-none');
                if (errorNotice) errorNotice.classList.add('d-none');

                try {
                    const response = await fetch(nextPageUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Server returned HTTP ' + response.status);
                    }

                    const data = await response.json();

                    if (data.html && data.html.trim().length > 0) {
                        const tempContainer = document.createElement('div');
                        tempContainer.innerHTML = data.html;

                        const newJobItems = Array.from(tempContainer.children);
                        newJobItems.forEach(item => {
                            grid.appendChild(item);
                        });

                        updateCounter(data.count || newJobItems.length);
                        if (totalCountEl && data.total) {
                            totalCountEl.textContent = data.total;
                        }
                    }

                    hasMore = Boolean(data.hasMore);
                    nextPageUrl = data.nextPageUrl;

                    if (hasMore && loadMoreWrapper) {
                        loadMoreWrapper.classList.remove('d-none');
                    }
                } catch (err) {
                    console.error('Failed to load opportunities dynamically:', err);
                    if (errorNotice) errorNotice.classList.remove('d-none');
                    if (loadMoreWrapper) loadMoreWrapper.classList.remove('d-none');
                } finally {
                    isLoading = false;
                    if (spinner) spinner.classList.add('d-none');

                    // Re-check immediately in case view is still near the bottom
                    setTimeout(checkScrollTrigger, 200);
                }
            }

            // Refresh Feed Action (Live synchronization on refresh)
            async function refreshFeed() {
                if (isLoading) return;
                isLoading = true;

                if (refreshIcon) refreshIcon.classList.add('spin-animation');
                if (refreshText) refreshText.textContent = 'Updating...';
                if (spinner) spinner.classList.remove('d-none');

                try {
                    const url = new URL(window.location.href);
                    url.searchParams.set('refresh', '1');
                    url.searchParams.set('page', '1');

                    const response = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('HTTP ' + response.status);

                    const data = await response.json();

                    if (data.html && data.html.trim().length > 0) {
                        grid.innerHTML = data.html;
                        currentlyLoadedCount = data.count || 12;
                        if (showingCountEl) showingCountEl.textContent = currentlyLoadedCount;
                        if (totalCountEl && data.total) totalCountEl.textContent = data.total;
                    }

                    nextPageUrl = data.nextPageUrl;
                    hasMore = Boolean(data.hasMore);

                    if (updateBanner) {
                        updateBanner.classList.remove('d-none');
                        setTimeout(() => updateBanner.classList.add('d-none'), 5000);
                    }

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } catch (err) {
                    console.error('Feed refresh error:', err);
                    window.location.href = '/jobs?refresh=1';
                } finally {
                    isLoading = false;
                    if (refreshIcon) refreshIcon.classList.remove('spin-animation');
                    if (refreshText) refreshText.textContent = 'Refresh Feed';
                    if (spinner) spinner.classList.add('d-none');
                }
            }

            // Click triggers
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', loadNextBatch);
            }
            if (retryBtn) {
                retryBtn.addEventListener('click', loadNextBatch);
            }
            if (refreshBtn) {
                refreshBtn.addEventListener('click', refreshFeed);
            }

            // Throttled scroll listener to guarantee continuous unlimited scrolling
            let scrollThrottle = null;
            window.addEventListener('scroll', function () {
                if (!scrollThrottle) {
                    scrollThrottle = setTimeout(function () {
                        scrollThrottle = null;
                        checkScrollTrigger();
                    }, 80);
                }
            }, { passive: true });

            window.addEventListener('resize', checkScrollTrigger, { passive: true });

            // IntersectionObserver for anticipatory loading
            if (sentinel && 'IntersectionObserver' in window) {
                const sentinelObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !isLoading) {
                            loadNextBatch();
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '450px',
                    threshold: 0.05
                });

                sentinelObserver.observe(sentinel);
            }

            // Initial check in case viewport is tall enough to see sentinel immediately
            setTimeout(checkScrollTrigger, 300);
        });
    </script>
@endsection