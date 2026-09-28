@extends('layouts.app')

@section('title', 'Interview Schedule - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
    <h1 class="page-title">Interview Schedule</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Round</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Job Title</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Company</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Type</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Date &amp; Time</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Result</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Meeting Link</th>
                </tr>
            </thead>
            <tbody>
                @forelse($interviews as $interview)
                    <tr>
                        <td class="px-4 py-4">
                            <span class="fw-semibold">Round {{ $interview->round_number }}</span>
                            <small class="d-block text-muted">{{ $interview->round_name }}</small>
                        </td>
                        <td class="px-4 py-4">
                            <a href="/applications/{{ $interview->application_id }}" class="fw-bold text-dark text-decoration-none">
                                {{ $interview->application->job->title ?? 'Position' }}
                            </a>
                        </td>
                        <td class="px-4 py-4 fw-medium text-secondary">{{ $interview->application->job->company->name ?? 'Company' }}</td>
                        <td class="px-4 py-4">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2 small">{{ ucfirst($interview->interview_type) }}</span>
                        </td>
                        <td class="px-4 py-4 small text-muted">
                            {{ $interview->scheduled_at ? \Carbon\Carbon::parse($interview->scheduled_at)->format('M d, Y h:i A') : 'TBD' }}
                        </td>
                        <td class="px-4 py-4">
                            <x-status-badge :status="$interview->result" />
                        </td>
                        <td class="px-4 py-4">
                            @if($interview->location_meeting_link)
                                <a href="{{ $interview->location_meeting_link }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Join
                                </a>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <x-empty-state title="No Scheduled Interviews" message="You will see your interview rounds here once shortlisted by employers." icon="bi-calendar-event" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($interviews->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <x-pagination :paginator="$interviews" />
        </div>
    @endif
</div>
@endsection