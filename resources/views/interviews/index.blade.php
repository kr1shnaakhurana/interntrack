@extends('layouts.dashboard')

@section('title', 'Interview Schedule - InternTrack')
@section('page_title', 'Scheduled Interview Rounds')

@section('content')
<div class="card card-custom bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Round</th>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Date & Time</th>
                    <th>Result</th>
                    <th>Meeting Link</th>
                </tr>
            </thead>
            <tbody>
                @forelse($interviews as $interview)
                    <tr>
                        <td>
                            <span class="fw-bold">Round {{ $interview->round_number }}</span>
                            <small class="d-block text-muted">{{ $interview->round_name }}</small>
                        </td>
                        <td>
                            <a href="/applications/{{ $interview->application_id }}" class="fw-bold text-dark text-decoration-none">
                                {{ $interview->application->job->title ?? 'Position' }}
                            </a>
                        </td>
                        <td class="fw-semibold text-secondary">
                            {{ $interview->application->job->company->name ?? 'Company' }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ ucfirst($interview->interview_type) }}</span>
                        </td>
                        <td class="small">
                            {{ $interview->scheduled_at ? \Carbon\Carbon::parse($interview->scheduled_at)->format('M d, Y h:i A') : 'TBD' }}
                        </td>
                        <td>
                            <x-status-badge :status="$interview->result" />
                        </td>
                        <td>
                            @if($interview->location_meeting_link)
                                <a href="{{ $interview->location_meeting_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Join
                                </a>
                            @else
                                <span class="text-muted small">N/A</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <x-empty-state title="No Scheduled Interviews" message="You will see your interview rounds here once shortlisted by employers." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$interviews" />
</div>
@endsection
