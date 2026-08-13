@extends('layouts.dashboard')

@section('title', 'Companies - InternTrack Admin')
@section('page_title', 'All Companies')

@section('content')
<div class="card card-custom bg-white p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
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
                            <div class="fw-bold text-dark">{{ $company->name }}</div>
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank" class="small text-primary text-decoration-none">
                                    {{ $company->website }}
                                </a>
                            @endif
                        </td>
                        <td class="small text-muted text-capitalize">{{ $company->sector ?? '—' }}</td>
                        <td>
                            @if($company->latestVerification)
                                <x-status-badge :status="$company->latestVerification->status" />
                            @else
                                <span class="badge bg-light text-muted border">Not Submitted</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $company->jobs->count() }} jobs</span>
                        </td>
                        <td>
                            @if($company->is_external)
                                <span class="badge bg-secondary-subtle text-secondary">External</span>
                            @else
                                <span class="badge bg-primary-subtle text-primary">Direct</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $company->created_at->format('M d, Y') }}</td>
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
