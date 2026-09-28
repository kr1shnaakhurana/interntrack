@props(['job'])

<div class="card job-card h-100 border-0 shadow-sm">
    <div class="card-body p-4 d-flex flex-column h-100">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div class="flex-grow-1">
                <h5 class="fw-bold mb-1">
                    <a href="/jobs/{{ $job->id }}" class="job-card-title text-decoration-none">{{ $job->title }}</a>
                </h5>
                <div class="d-flex align-items-center flex-wrap gap-2 mt-1">
                    <span class="job-card-company">{{ $job->company->name ?? 'External Company' }}</span>
                    @if(($job->company->latestVerification->status ?? '') === 'approved')
                        <span class="job-card-verified badge rounded-pill px-2 py-1">
                            <i class="bi bi-patch-check-fill me-1"></i> Verified
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex-shrink-0">
                <x-status-badge :type="$job->job_type" />
            </div>
        </div>

        <div class="flex-grow-1 d-flex flex-column">
            <div class="job-card-meta d-flex align-items-center flex-wrap gap-2 mb-2">
                <span><i class="bi bi-geo-alt me-1"></i> {{ $job->location ?? 'Multiple Locations' }}</span>
                @if($job->is_remote)
                    <span class="job-card-remote badge rounded-pill px-2 py-1">Remote</span>
                @endif
            </div>

            @if($job->stipend_salary)
                <div class="job-card-pay fw-semibold mb-1">
                    <i class="bi bi-cash-stack me-1"></i> {{ $job->stipend_salary }}
                </div>
            @endif

            @if($job->deadline)
                <div class="job-card-meta">
                    <i class="bi bi-clock-history me-1"></i> Apply by {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}
                </div>
            @endif
        </div>

        <div class="pt-3 border-top d-flex align-items-center justify-content-between mt-auto">
            @if($job->source && $job->source->type === 'direct')
                <span class="job-card-source badge rounded-pill px-2 py-1">InternTrack Direct</span>
            @else
                <span class="job-card-source-alt badge rounded-pill px-2 py-1 border">{{ $job->source->name ?? 'External' }}</span>
            @endif

            <a href="/jobs/{{ $job->id }}" class="job-card-cta btn btn-primary btn-sm rounded-pill px-4">View Job</a>
        </div>
    </div>
</div>
