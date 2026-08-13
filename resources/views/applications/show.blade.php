@extends('layouts.dashboard')

@section('title', 'Application Details - InternTrack')
@section('page_title', 'Application Details & Status Timeline')

@section('content')
<div class="mb-3">
    <a href="/applications" class="text-decoration-none text-muted small">&larr; Back to Applications</a>
</div>

<div class="row g-4">
    <!-- Main Application Timeline Card -->
    <div class="col-md-8">
        <div class="card card-custom bg-white p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
                <div>
                    <h3 class="fw-bold mb-1">{{ $application->job->title }}</h3>
                    <div class="fs-5 text-secondary fw-semibold">{{ $application->job->company->name ?? 'Company' }}</div>
                </div>
                <x-status-badge :status="$application->status" />
            </div>

            <!-- VISUAL APPLICATION TIMELINE -->
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history text-primary me-2"></i> Application Journey Timeline</h5>
            <div class="position-relative ps-4 ms-2 mb-5" style="border-left: 2px solid #e2e8f0;">
                
                @forelse($application->statusHistory as $history)
                    <div class="position-relative mb-4">
                        <div class="position-absolute translate-middle-x bg-primary rounded-circle" style="left: -25px; top: 4px; width: 14px; height: 14px; border: 3px solid white;"></div>
                        <div class="fw-bold text-dark">Status: {{ ucfirst($history->status) }}</div>
                        <div class="small text-muted mb-1">{{ \Carbon\Carbon::parse($history->changed_at)->format('M d, Y h:i A') }} • Changed by {{ $history->changedByUser->name ?? 'System' }}</div>
                        @if($history->remarks)
                            <div class="small bg-light p-2 rounded text-secondary border">{{ $history->remarks }}</div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted small">No status history recorded yet.</div>
                @endforelse

            </div>

            <!-- Scheduled Interview Rounds -->
            <h5 class="fw-bold mb-3"><i class="bi bi-calendar-event text-success me-2"></i> Interview Rounds</h5>
            <div class="list-group mb-3">
                @forelse($application->interviews as $interview)
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                        <div>
                            <div class="fw-bold text-dark">Round {{ $interview->round_number }}: {{ $interview->round_name }}</div>
                            <div class="small text-muted">
                                <i class="bi bi-person me-1"></i> Interviewer: {{ $interview->interviewer ?? 'TBD' }} | 
                                <i class="bi bi-camera-video me-1"></i> Type: {{ ucfirst($interview->interview_type) }}
                            </div>
                            @if($interview->location_meeting_link)
                                <a href="{{ $interview->location_meeting_link }}" target="_blank" class="small text-primary text-decoration-none d-block mt-1">
                                    <i class="bi bi-link-45deg me-1"></i> Join Meeting Link
                                </a>
                            @endif
                        </div>
                        <div>
                            <x-status-badge :status="$interview->result" />
                        </div>
                    </div>
                @empty
                    <div class="text-muted small p-3 bg-light rounded border">No interview rounds scheduled yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Application Details Sidebar -->
    <div class="col-md-4">
        <div class="card card-custom bg-white p-4">
            <h5 class="fw-bold mb-3">Submission Info</h5>
            
            <div class="mb-3">
                <small class="text-muted d-block">APPLIED DATE</small>
                <span class="fw-semibold">{{ \Carbon\Carbon::parse($application->applied_at ?? $application->created_at)->format('M d, Y') }}</span>
            </div>

            @if($application->cover_letter_url)
                <div class="mb-3">
                    <small class="text-muted d-block">RESUME / COVER LETTER</small>
                    <a href="{{ $application->cover_letter_url }}" target="_blank" class="btn btn-outline-primary btn-sm mt-1 w-100">
                        <i class="bi bi-file-earmark-pdf me-1"></i> View Document
                    </a>
                </div>
            @endif

            @if($application->expected_salary)
                <div class="mb-3">
                    <small class="text-muted d-block">EXPECTED STIPEND / SALARY</small>
                    <span class="fw-semibold text-success">{{ $application->expected_salary }}</span>
                </div>
            @endif

            @if($application->notes)
                <div class="mb-3">
                    <small class="text-muted d-block">CANDIDATE NOTES</small>
                    <p class="small text-secondary mb-0 bg-light p-2 rounded border">{{ $application->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
