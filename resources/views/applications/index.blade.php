@extends('layouts.dashboard')

@section('title', 'My Applications - InternTrack')
@section('page_title', 'My Application Tracker')

@section('content')
<div class="card card-custom bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Applied Date</th>
                    <th>Next Interview</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>
                            <a href="/applications/{{ $app->id }}" class="fw-bold text-dark text-decoration-none">
                                {{ $app->job->title }}
                            </a>
                        </td>
                        <td>
                            <span class="fw-semibold text-secondary">{{ $app->job->company->name ?? 'External Company' }}</span>
                            @if(($app->job->company->latestVerification->status ?? '') === 'approved')
                                <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
                            @endif
                        </td>
                        <td>
                            <x-status-badge :status="$app->status" />
                        </td>
                        <td class="small text-muted">
                            {{ $app->created_at->format('M d, Y') }}
                        </td>
                        <td class="small">
                            @php $nextInterview = $app->interviews->where('result', 'pending')->first(); @endphp
                            @if($nextInterview)
                                <span class="badge bg-primary-subtle text-primary">
                                    <i class="bi bi-calendar me-1"></i> {{ $nextInterview->round_name }} ({{ \Carbon\Carbon::parse($nextInterview->scheduled_at)->format('M d') }})
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="/applications/{{ $app->id }}" class="btn btn-sm btn-outline-primary fw-semibold">View Timeline</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <x-empty-state title="No Applications Submitted" message="Start applying for open internship and job opportunities." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$applications" />
</div>
@endsection
