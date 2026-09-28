@extends('layouts.dashboard')

@section('title', 'All Jobs - InternTrack Admin')
@section('page_title', 'All Job Listings')

@section('content')
<div class="bg-white rounded-xl p-3 shadow-sm border border-gray-200">
    <div class="table-responsive">
        <table class="w-full rounded-lg overflow-auto border border-gray-200">
            <thead class="table-light">
                <tr>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Posted</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>
                            <a href="/jobs/{{ $job->id }}" class="font-bold text-gray-900 text-decoration-none">{{ $job->title }}</a>
                            @if($job->location)
                                <div class="small text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $job->location }}</div>
                            @endif
                        </td>
                        <td class="font-semibold text-gray-500">{{ $job->company->name ?? 'External' }}</td>
                        <td><x-status-badge :type="$job->job_type" /></td>
                        <td>
                            @if($job->source)
                                <span class="badge {{ $job->source->type === 'direct' ? 'bg-indigo-100 text-indigo-800 rounded-pill' : 'bg-gray-100 text-gray-500 rounded-pill' }} border">
                                    {{ $job->source->name }}
                                </span>
                            @else
                                <span class="text-muted small">?</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $job->is_active ? 'bg-indigo-100 text-indigo-800 rounded-pill' : 'bg-gray-100 text-gray-500 rounded-pill' }}">
                                {{ $job->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-muted small">
                            {{ $job->posted_at ? \Carbon\Carbon::parse($job->posted_at)->format('M d, Y') : $job->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">No job listings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$jobs" />
</div>
@endsection


