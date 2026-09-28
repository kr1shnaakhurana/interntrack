@extends('layouts.dashboard')

@section('title', 'Companies - InternTrack Admin')
@section('page_title', 'All Companies')

@section('content')
<div class="bg-white rounded-xl p-3 shadow-sm border border-gray-200">
    <div class="table-responsive">
        <table class="w-full rounded-lg overflow-auto border border-gray-200">
            <thead class="table-light">
                <tr>
                    <th>Company Name</th>
                    <th>Sector</th>
                    <th>Verification</th>
                    <th>Jobs Posted</th>
                    <th>External</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td>
                            <div class="font-bold text-gray-900">{{ $company->name }}</div>
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank" class="small text-indigo-600 text-decoration-none">
                                    {{ $company->website }}
                                </a>
                            @endif
                        </td>
                        <td class="text-muted small text-capitalize">{{ $company->sector ?? '�' }}</td>
                        <td>
                            @if($company->latestVerification)
                                <x-status-badge :status="$company->latestVerification->status" />
                            @else
                                <span class="badge bg-gray-100 text-muted rounded-pill border border-gray-200">Not Submitted</span>
                            @endif
                        </td>
                        <td>
                            <span class="bg-gray-100 text-gray-600 rounded-pill px-2 py-1 small border border-gray-200">{{ $company->jobs->count() }} jobs</span>
                        </td>
                        <td>
                            @if($company->is_external)
                                <span class="badge bg-gray-100 text-gray-500 rounded-pill px-2 py-1 small border border-gray-200">External</span>
                            @else
                                <span class="badge bg-indigo-100 text-indigo-800 rounded-pill px-2 py-1 small border border-indigo-100">Direct</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $company->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">No companies registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$companies" />
</div>
@endsection
