@extends('layouts.app')

@section('title', 'Application Details - InternTrack')

@section('content')
<div class="mb-4">
    <a href="/applications" class="text-decoration-none text-muted small fw-medium">&larr; Back to Applications</a>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                    <div>
                        <h3 class="fw-bold text-dark mb-2">{{ $application->job->title }}</h3>
                        <div class="fw-semibold text-secondary fs-5">{{ $application->job->company->name ?? 'Company' }}</div>
                    </div>
                    <x-status-badge :status="$application->status" />
                </div>

                <h5 class="fw-bold mb-4 text-secondary"><i class="bi bi-clock-history me-2 text-primary"></i> Application Journey Timeline</h5>
                <div class="timeline position-relative ps-4 mb-5">
                    @forelse($application->statusHistory as $history)
                        <div class="position-relative mb-4">
                            <div class="timeline-dot position-absolute translate-middle rounded-circle bg-primary"></div>
                            <div class="fw-bold text-dark">Status: {{ ucfirst($history->status) }}</div>
                            <div class="small text-muted mb-1">{{ \Carbon\Carbon::parse($history->changed_at)->format('M d, Y h:i A') }} — Changed by {{ $history->changedByUser->name ?? 'System' }}</div>
                            @if($history->remarks)
                                <div class="bg-light rounded px-3 py-2 text-secondary small">{{ $history->remarks }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted small">No status history recorded yet.</div>
                    @endforelse
                </div>

                <h5 class="fw-bold mb-4 text-secondary"><i class="bi bi-calendar-event me-2 text-success"></i> Interview Rounds</h5>
                <div class="list-group list-group-flush">
                    @forelse($application->interviews as $interview)
                        <div class="list-group-item px-0 py-3 border-0 border-bottom">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                <div>
                                    <div class="fw-bold text-dark">Round {{ $interview->round_number }}: {{ $interview->round_name }}</div>
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-person me-1"></i> Interviewer: {{ $interview->interviewer ?? 'TBD' }} |
                                        <i class="bi bi-camera-video me-1"></i> Type: {{ ucfirst($interview->interview_type) }}
                                    </div>
                                    @if($interview->location_meeting_link)
                                        <a href="{{ $interview->location_meeting_link }}" target="_blank" class="small text-primary text-decoration-none mt-2 d-inline-block">
                                            <i class="bi bi-link-45deg me-1"></i> Join Meeting Link
                                        </a>
                                    @endif
                                </div>
                                <div class="text-md-end">
                                    <x-status-badge :status="$interview->result" />
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small py-3 bg-light rounded border">No interview rounds scheduled yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm sticky-top sticky-100">
            <div class="card-body p-4 p-md-5">
                <h5 class="fw-bold text-secondary mb-4">Submission Info</h5>

                <div class="mb-4">
                    <small class="text-muted d-block text-uppercase fw-medium">Applied Date</small>
                    <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($application->applied_at ?? $application->created_at)->format('M d, Y') }}</span>
                </div>

                @if($application->cover_letter_url)
                    <div class="mb-4">
                        <small class="text-muted d-block text-uppercase fw-medium">Resume / Cover Letter</small>
                        <a href="{{ $application->cover_letter_url }}" target="_blank" class="btn btn-outline-primary w-100 rounded-pill py-2 mt-1">
                            <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                        </a>
                    </div>
                @endif

                @if($application->expected_salary)
                    <div class="mb-4">
                        <small class="text-muted d-block text-uppercase fw-medium">Expected Stipend / Salary</small>
                        <span class="fw-semibold text-success">{{ $application->expected_salary }}</span>
                    </div>
                @endif

                @if($application->notes)
                    <div class="mb-4">
                        <small class="text-muted d-block text-uppercase fw-medium">Candidate Notes</small>
                        <p class="small text-secondary bg-light rounded p-3 mb-0">{{ $application->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline { border-left: 2px solid var(--border-color); }
    .timeline > div:last-child { margin-bottom: 0 !important; }
    .timeline-dot {
        left: -25px;
        top: 12px;
        width: 14px;
        height: 14px;
        border: 3px solid #fff;
    }
</style>
@endpush