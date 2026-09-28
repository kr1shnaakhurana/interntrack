@extends('layouts.app')

@section('title', 'My Applications - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <h1 class="page-title">My Applications</h1>
    <a href="/jobs" class="btn btn-primary rounded-pill px-4"><i class="bi bi-plus-circle me-1"></i> Browse Jobs</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Job Title</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Company</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Status</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Applied Date</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Next Interview</th>
                    <th class="fw-semibold text-secondary small text-uppercase text-nowrap px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td class="px-4 py-4">
                            <a href="/applications/{{ $app->id }}" class="fw-bold text-dark text-decoration-none">{{ $app->job->title }}</a>
                        </td>
                        <td class="px-4 py-4">
                            <span class="fw-medium text-secondary">{{ $app->job->company->name ?? 'External Company' }}</span>
                            @if(($app->job->company->latestVerification->status ?? '') === 'approved')
                                <i class="bi bi-patch-check-fill text-primary ms-1" title="Verified"></i>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <x-status-badge :status="$app->status" />
                        </td>
                        <td class="px-4 py-4 small text-muted">{{ $app->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-4">
                            @php $nextInterview = $app->interviews->where('result', 'pending')->first(); @endphp
                            @if($nextInterview)
                                <span class="badge bg-primary-light text-primary rounded-pill px-3 py-2 small">
                                    <i class="bi bi-calendar me-1"></i> {{ $nextInterview->round_name }} ({{ \Carbon\Carbon::parse($nextInterview->scheduled_at)->format('M d') }})
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <a href="/applications/{{ $app->id }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">View Timeline</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <x-empty-state title="No Applications Submitted" message="Start applying for open internship and job opportunities." icon="bi-journal-check" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        <x-pagination :paginator="$applications" />
    </div>
</div>
@endsection