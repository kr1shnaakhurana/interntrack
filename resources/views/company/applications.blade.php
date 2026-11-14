@extends('layouts.dashboard')

@section('title', 'Company Applicants - InternTrack')
@section('page_title', 'Candidate Applications')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <div>
        <p class="text-muted small mb-0">Review candidate profiles, resume submissions, and interview progression for your organization's roles.</p>
    </div>
    <div class="text-muted small">
        Showing <strong class="text-dark">{{ $applications->total() }}</strong> total candidate submissions
    </div>
</div>

<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Opportunity</th>
                    <th>Status</th>
                    <th>Submission Date</th>
                    <th>Interviews</th>
                    <th class="text-end">Resume</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $app->student->user->name ?? 'Candidate' }}</div>
                            <div class="text-muted small">{{ $app->student->program ?? $app->student->user->email ?? '' }}</div>
                        </td>
                        <td>
                            <a href="/jobs/{{ $app->job->id }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $app->job->title }}
                            </a>
                        </td>
                        <td><x-status-badge :status="$app->status" /></td>
                        <td class="text-muted small">{{ $app->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="badge badge-subtle-primary">
                                {{ $app->interviews->count() }} round(s)
                            </span>
                        </td>
                        <td class="text-end">
                            @if($app->cover_letter_url)
                                <a href="{{ $app->cover_letter_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-file-earmark-pdf"></i> View Resume
                                </a>
                            @else
                                <span class="text-muted small">Not provided</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <x-empty-state title="No Applicants Yet" message="Candidate submissions will appear here once candidates apply to your job postings." icon="bi-people" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$applications" />
@endsection
