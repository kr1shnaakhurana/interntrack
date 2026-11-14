@extends('layouts.app')

@section('title', 'Application Details - InternTrack')

@section('content')
<div class="mb-3">
    <a href="/applications" class="text-muted small fw-medium text-decoration-none d-inline-flex align-items-center gap-1">
        <i class="bi bi-arrow-left"></i> Back to Applications
    </a>
</div>

<div class="row g-3">
    <!-- Main Application Progress Column -->
    <div class="col-12 col-lg-8">
        <div class="card mb-3">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 pb-3 mb-4 border-bottom">
                    <div>
                        <h1 class="page-title fs-5 mb-1">{{ $application->job->title }}</h1>
                        <div class="fw-medium text-secondary small">{{ $application->job->company->name ?? 'Company' }}</div>
                    </div>
                    <div class="flex-shrink-0">
                        <x-status-badge :status="$application->status" />
                    </div>
                </div>

                <!-- Timeline Section -->
                <div class="mb-4">
                    <h2 class="section-title fs-6 mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-muted"></i>
                        Application Journey Timeline
                    </h2>
                    <div class="timeline-track">
                        @forelse($application->statusHistory as $history)
                            <div class="timeline-step">
                                <div class="timeline-marker {{ in_array($history->status, ['selected', 'approved', 'cleared']) ? 'done' : '' }}"></div>
                                <div class="d-flex flex-wrap align-items-baseline gap-2">
                                    <span class="fw-semibold text-dark small">Status: {{ ucfirst(str_replace('_', ' ', $history->status)) }}</span>
                                    <span class="text-muted small" style="font-size: 11.5px;">
                                        {{ \Carbon\Carbon::parse($history->changed_at)->format('M d, Y h:i A') }} &bull; Updated by {{ $history->changedByUser->name ?? 'System' }}
                                    </span>
                                </div>
                                @if($history->remarks)
                                    <div class="mt-1 p-2 bg-light border rounded-1 text-secondary small" style="font-size: 12.5px;">
                                        {{ $history->remarks }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-muted small">No status history recorded yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Interview Rounds Section -->
                <div class="pt-3 border-top">
                    <h2 class="section-title fs-6 mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event text-muted"></i>
                        Interview Stages
                    </h2>
                    <div class="border rounded-1 overflow-hidden">
                        <div class="table-responsive">
                            <table class="table mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th>Round</th>
                                        <th>Type &amp; Interviewer</th>
                                        <th>Scheduled Date</th>
                                        <th>Result</th>
                                        <th class="text-end">Meeting Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($application->interviews as $interview)
                                        <tr>
                                            <td>
                                                <span class="fw-semibold text-dark">Round {{ $interview->round_number }}</span>
                                                <div class="text-muted small">{{ $interview->round_name }}</div>
                                            </td>
                                            <td>
                                                <div class="text-dark small text-capitalize">{{ $interview->interview_type }}</div>
                                                <div class="text-muted small">{{ $interview->interviewer ?? 'Interviewer TBD' }}</div>
                                            </td>
                                            <td class="small text-muted">
                                                {{ $interview->scheduled_at ? \Carbon\Carbon::parse($interview->scheduled_at)->format('M d, Y h:i A') : 'TBD' }}
                                            </td>
                                            <td>
                                                <x-status-badge :status="$interview->result" />
                                            </td>
                                            <td class="text-end">
                                                @if($interview->location_meeting_link)
                                                    <a href="{{ $interview->location_meeting_link }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-box-arrow-up-right"></i> Join Meeting
                                                    </a>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-3 text-muted small">
                                                No interview rounds scheduled yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submission Details Sidebar -->
    <div class="col-12 col-lg-4">
        <div class="card sticky-top" style="top: 76px;">
            <div class="card-header py-3">
                <span class="fw-semibold fs-6 text-dark">Submission Metadata</span>
            </div>
            <div class="card-body p-3">
                <div class="mb-3">
                    <div class="text-muted small" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">Submitted On</div>
                    <div class="fw-medium text-dark small mt-0.5">
                        {{ \Carbon\Carbon::parse($application->applied_at ?? $application->created_at)->format('M d, Y') }}
                    </div>
                </div>

                @if($application->cover_letter_url)
                    <div class="mb-3">
                        <div class="text-muted small" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">Uploaded Resume</div>
                        <div class="mt-1">
                            <a href="{{ $application->cover_letter_url }}" target="_blank" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-file-earmark-pdf"></i> View Submitted Document
                            </a>
                        </div>
                    </div>
                @endif

                @if($application->expected_salary)
                    <div class="mb-3">
                        <div class="text-muted small" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">Stipend Expectation</div>
                        <div class="fw-semibold text-success small mt-0.5">{{ $application->expected_salary }}</div>
                    </div>
                @endif

                @if($application->notes)
                    <div class="mb-2">
                        <div class="text-muted small" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">Candidate Notes</div>
                        <p class="small text-secondary bg-light border p-2 rounded-1 mt-1 mb-0">{{ $application->notes }}</p>
                    </div>
                @endif

                <div class="pt-3 border-top mt-3">
                    <a href="/jobs/{{ $application->job_id }}" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-briefcase"></i> View Job Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection