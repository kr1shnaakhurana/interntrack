@extends('layouts.app')

@section('title', 'Interview Schedule - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Interview Schedule</h1>
        <p class="text-muted small mb-0">Scheduled interview rounds, technical assessments, and panel discussions.</p>
    </div>
    <a href="/applications" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-journal-check"></i> View Applications
    </a>
</div>

<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Round</th>
                    <th>Opportunity</th>
                    <th>Employer</th>
                    <th>Format</th>
                    <th>Scheduled Date &amp; Time</th>
                    <th>Result</th>
                    <th class="text-end">Meeting Link</th>
                </tr>
            </thead>
            <tbody>
                @forelse($interviews as $interview)
                    <tr>
                        <td>
                            <span class="fw-semibold text-dark">Round {{ $interview->round_number }}</span>
                            <div class="text-muted small">{{ $interview->round_name }}</div>
                        </td>
                        <td>
                            <a href="/applications/{{ $interview->application_id }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $interview->application->job->title ?? 'Position' }}
                            </a>
                        </td>
                        <td>
                            <span class="text-secondary">{{ $interview->application->job->company->name ?? 'Company' }}</span>
                        </td>
                        <td>
                            <span class="badge badge-subtle-neutral text-capitalize">{{ $interview->interview_type }}</span>
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
                                    <i class="bi bi-box-arrow-up-right"></i> Join Link
                                </a>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <x-empty-state title="No Scheduled Interviews" message="You do not have any pending or completed interview rounds yet." icon="bi-calendar-event">
                                <div class="mt-3">
                                    <a href="/jobs" class="btn btn-primary btn-sm">Explore Opportunities</a>
                                </div>
                            </x-empty-state>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection