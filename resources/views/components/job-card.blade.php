@props(['job'])

<div class="card card-custom h-100 p-3">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
            <h5 class="fw-bold mb-1">
                <a href="/jobs/{{ $job->id }}" class="text-decoration-none text-dark">{{ $job->title }}</a>
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="fw-semibold text-secondary">{{ $job->company->name ?? 'External Company' }}</span>
                @if(($job->company->latestVerification->status ?? '') === 'approved')
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                        <i class="bi bi-patch-check-fill me-1"></i> Verified
                    </span>
                @endif
            </div>
        </div>
        <x-status-badge :type="$job->job_type" />
    </div>

    <div class="my-2">
        <div class="text-muted small mb-1">
            <i class="bi bi-geo-alt me-1"></i> {{ $job->location ?? 'Multiple Locations' }}
            @if($job->is_remote)
                <span class="badge bg-info-subtle text-info ms-2">Remote Work</span>
            @endif
        </div>

        @if($job->stipend_salary)
            <div class="fw-semibold text-success small mb-1">
                <i class="bi bi-cash-stack me-1"></i> {{ $job->stipend_salary }}
            </div>
        @endif

        @if($job->deadline)
            <div class="text-muted small">
                <i class="bi bi-clock-history me-1"></i> Apply by {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}
            </div>
        @endif
    </div>

    <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
        <small class="text-muted">
            @if($job->source && $job->source->type === 'direct')
                <span class="badge bg-primary-subtle text-primary">Source: InternTrack</span>
            @else
                <span class="badge bg-secondary-subtle text-secondary">Source: {{ $job->source->name ?? 'External' }}</span>
            @endif
        </small>

        <a href="/jobs/{{ $job->id }}" class="btn btn-outline-primary btn-sm px-3 fw-semibold">View Job</a>
    </div>
</div>
