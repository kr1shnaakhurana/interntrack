@props(['job'])

<div class="card job-card h-100">
    <div class="card-body p-3 d-flex flex-column h-100">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
            <div class="flex-grow-1 min-w-0">
                <h5 class="fs-6 fw-semibold mb-1 text-truncate">
                    <a href="/jobs/{{ $job->id }}" class="text-dark text-decoration-none">{{ $job->title }}</a>
                </h5>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <span
                        class="text-secondary small fw-medium">{{ $job->company->name ?? 'External Organization' }}</span>
                    @if(($job->company->latestVerification->status ?? '') === 'approved')
                        <span class="badge badge-subtle-primary" title="Verified Employer">
                            <i class="bi bi-patch-check-fill"></i> Verified
                        </span>
                    @elseif(($job->company->sector ?? '') === 'government' || ($job->job_type ?? '') === 'government')
                        <span class="badge badge-subtle-success" title="Government Recruitment">
                            <i class="bi bi-bank"></i> Govt
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex-shrink-0">
                <x-status-badge :type="$job->job_type" />
            </div>
        </div>

        <div class="flex-grow-1 d-flex flex-column gap-1 my-2">
            <div class="text-muted small d-flex align-items-center flex-wrap gap-2">
                <span><i class="bi bi-geo-alt me-1"></i>{{ $job->location ?? 'Not Specified' }}</span>
                @if($job->is_remote)
                    <span class="badge badge-subtle-success">Remote</span>
                @endif
                @if(!empty($job->metadata['vacancies_count']))
                    <span class="badge badge-subtle-primary">{{ $job->metadata['vacancies_count'] }}</span>
                @endif
            </div>

            @if($job->stipend_salary)
                <div class="small fw-semibold text-success">
                    <i class="bi bi-cash-stack me-1"></i>{{ $job->stipend_salary }}
                </div>
            @endif

            @if($job->deadline)
                <div class="text-muted small">
                    <i class="bi bi-calendar3 me-1"></i> Deadline:
                    {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}
                </div>
            @endif
        </div>

        <div class="pt-2 mt-auto border-top d-flex align-items-center justify-content-between gap-2">
            @if($job->source && $job->source->type === 'direct')
                <span class="badge badge-subtle-primary">InternTrack Direct</span>
            @elseif($job->source)
                <span class="badge badge-subtle-neutral">
                    <i class="bi bi-globe2 me-1"></i>{{ $job->source->name }}
                </span>
            @else
                <span class="badge badge-subtle-neutral">External</span>
            @endif

            <a href="/jobs/{{ $job->id }}" class="btn btn-outline-primary btn-sm">View Details</a>
        </div>
    </div>
</div>