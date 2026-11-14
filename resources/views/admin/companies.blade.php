@extends('layouts.dashboard')

@section('title', 'All Companies - InternTrack Admin')
@section('page_title', 'Registered Organizations')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <div>
        <p class="text-muted small mb-0">Directory of verified employers, partner organizations, and external aggregated companies.</p>
    </div>
    <div class="text-muted small">
        Showing <strong class="text-dark">{{ $companies->total() }}</strong> total organizations
    </div>
</div>

<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Organization</th>
                    <th>Sector</th>
                    <th>Verification</th>
                    <th>Postings</th>
                    <th>Source Type</th>
                    <th class="text-end">Registered</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $company->name }}</div>
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank" class="small text-muted text-decoration-none d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-link-45deg"></i> {{ $company->website }}
                                </a>
                            @endif
                        </td>
                        <td class="small text-secondary text-capitalize">{{ $company->sector ?? '—' }}</td>
                        <td>
                            @if($company->latestVerification)
                                <x-status-badge :status="$company->latestVerification->status" />
                            @else
                                <span class="badge badge-subtle-neutral">Not Submitted</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-subtle-primary">{{ $company->jobs->count() }} jobs</span>
                        </td>
                        <td>
                            @if($company->is_external)
                                <span class="badge badge-subtle-neutral">External Partner</span>
                            @else
                                <span class="badge badge-subtle-primary">Direct Employer</span>
                            @endif
                        </td>
                        <td class="small text-muted text-end">{{ $company->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <x-empty-state title="No Companies Found" message="There are no company records registered in the system." icon="bi-building" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$companies" />
@endsection
