@extends('layouts.app')

@section('title', 'My Applications - InternTrack')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Candidate Applications</h1>
        <p class="text-muted small mb-0">Track application stages, reviews, and scheduled interview rounds.</p>
    </div>
    <a href="/jobs" class="btn btn-primary btn-sm">
        <i class="bi bi-briefcase"></i> Browse Opportunities
    </a>
</div>

<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Job Position</th>
                    <th>Employer</th>
                    <th>Status</th>
                    <th>Submission Date</th>
                    <th>Upcoming Round</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>
                            <a href="/applications/{{ $app->id }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $app->job->title }}
                            </a>
                        </td>
                        <td>
                            <span class="text-secondary">{{ $app->job->company->name ?? 'External Company' }}</span>
                            @if(($app->job->company->latestVerification->status ?? '') === 'approved')
                                <i class="bi bi-patch-check-fill text-primary ms-1" title="Verified Employer"></i>
                            @endif
                        </td>
                        <td>
                            <x-status-badge :status="$app->status" />
                        </td>
                        <td class="text-muted small">{{ $app->created_at->format('M d, Y') }}</td>
                        <td>
                            @php $nextInterview = $app->interviews->where('result', 'pending')->first(); @endphp
                            @if($nextInterview)
                                <span class="badge badge-subtle-primary">
                                    <i class="bi bi-calendar3"></i> {{ $nextInterview->round_name }} ({{ \Carbon\Carbon::parse($nextInterview->scheduled_at)->format('M d') }})
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="/applications/{{ $app->id }}" class="btn btn-outline-secondary btn-sm">
                                View Timeline
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <x-empty-state title="No Applications Submitted" message="You have not applied for any roles yet. Explore open internships to start." icon="bi-journal-check">
                                <div class="mt-3">
                                    <a href="/jobs" class="btn btn-primary btn-sm">Browse Opportunities</a>
                                </div>
                            </x-empty-state>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$applications" />
@endsection