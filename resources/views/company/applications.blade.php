@extends('layouts.dashboard')

@section('title', 'Company Applicants - InternTrack')
@section('page_title', 'All Applicants')

@section('content')
<div class="bg-white rounded-xl p-3 shadow-sm border border-gray-200">
    <div class="table-responsive">
        <table class="w-full rounded-lg overflow-auto border border-gray-200">
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
                            <div class="font-bold text-gray-900">{{ $app->student->user->name ?? 'Candidate' }}</div>
                            <div class="small text-gray-500">{{ $app->student->program ?? '' }}</div>
                        </td>
                        <td>
                            <a href="/jobs/{{ $app->job->id }}" class="font-semibold text-gray-900 text-decoration-none">{{ $app->job->title }}</a>
                        </td>
                        <td><x-status-badge :status="$app->status" /></td>
                        <td class="text-muted small">{{ $app->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="bg-indigo-100 text-indigo-800 rounded-pill px-2 py-1 text-xs border border-indigo-100">{{ $app->interviews->count() }} round(s)</span>
                        </td>
                        <td>
                            @if($app->cover_letter_url)
                                <a href="{{ $app->cover_letter_url }}" target="_blank" class="inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View
                                </a>
                            @else
                                <span class="text-gray-400 small">Not uploaded</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-400 py-4">
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


