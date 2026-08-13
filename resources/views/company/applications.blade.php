@extends('layouts.dashboard')

@section('title', 'Company Applicants - InternTrack')
@section('page_title', 'All Applicants')

@section('content')
<div class="card card-custom bg-white p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Candidate Name</th>
                    <th>Job Position</th>
                    <th>Application Status</th>
                    <th>Applied Date</th>
                    <th>Interviews</th>
                    <th>Resume</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $app->student->user->name ?? 'Candidate' }}</div>
                            <div class="small text-muted">{{ $app->student->program ?? '' }}</div>
                        </td>
                        <td>
                            <a href="/jobs/{{ $app->job->id }}" class="fw-semibold text-dark text-decoration-none">{{ $app->job->title }}</a>
                        </td>
                        <td><x-status-badge :status="$app->status" /></td>
                        <td class="small text-muted">{{ $app->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $app->interviews->count() }} round(s)</span>
                        </td>
                        <td>
                            @if($app->cover_letter_url)
                                <a href="{{ $app->cover_letter_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View
                                </a>
                            @else
                                <span class="text-muted small">Not uploaded</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <x-empty-state title="No Applicants Yet" message="Applicants will appear here once candidates apply to your job postings." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$applications" />
</div>
@endsection
