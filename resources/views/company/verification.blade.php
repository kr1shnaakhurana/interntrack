@extends('layouts.dashboard')

@section('title', 'Organisation Verification - InternTrack')
@section('page_title', 'Organisation Verification')

@section('content')
<!-- Current Status Banner -->
@php
    $latest = $company->latestVerification ?? null;
    $status = $latest?->status ?? 'not_submitted';
@endphp

<div class="card card-custom bg-white p-4 mb-4">
    <div class="d-flex align-items-center gap-3">
        @if($status === 'approved')
            <i class="bi bi-patch-check-fill fs-2 text-success"></i>
            <div>
                <h5 class="fw-bold text-success mb-1">Verified Organisation</h5>
                <p class="mb-0 text-muted small">Your organisation is verified. You may publish direct job opportunities.</p>
            </div>
        @elseif($status === 'pending')
            <i class="bi bi-hourglass-split fs-2 text-warning"></i>
            <div>
                <h5 class="fw-bold text-warning mb-1">Verification Under Review</h5>
                <p class="mb-0 text-muted small">Submitted on {{ $latest?->created_at?->format('M d, Y') }}. An admin will review within 2–3 business days.</p>
            </div>
        @elseif($status === 'rejected')
            <i class="bi bi-x-circle-fill fs-2 text-danger"></i>
            <div>
                <h5 class="fw-bold text-danger mb-1">Verification Rejected</h5>
                <p class="mb-0 text-muted small">Reason: {{ $latest?->remarks ?? 'See history below.' }}</p>
                <small class="text-muted">You may resubmit with updated documentation.</small>
            </div>
        @else
            <i class="bi bi-shield-exclamation fs-2 text-secondary"></i>
            <div>
                <h5 class="fw-bold mb-1">Not Yet Verified</h5>
                <p class="mb-0 text-muted small">Submit a verification request to unlock job publishing.</p>
            </div>
        @endif
    </div>
</div>

<!-- Submit Verification Form -->
@if($status !== 'approved' && $status !== 'pending')
    <div class="card card-custom bg-white p-4 mb-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-send text-primary me-2"></i>Submit Verification Request</h5>
        <form action="/company/verification" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold small">Document / Evidence URL</label>
                <input type="url" name="documents_url" class="form-control" required
                    placeholder="https://drive.google.com/file/... or https://storage.com/cert.pdf">
                <small class="text-muted">Link to your registration certificate, GST document, or official letter.</small>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold small">Additional Remarks (Optional)</label>
                <textarea name="remarks" class="form-control" rows="3"
                    placeholder="Briefly describe your organisation and the attached documents..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary fw-semibold px-4">
                <i class="bi bi-send me-1"></i> Submit for Review
            </button>
        </form>
    </div>
@endif

<!-- Verification History -->
@if($verifications->count() > 0)
    <div class="card card-custom bg-white p-4">
        <h5 class="fw-bold mb-3">Verification History</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Reviewed By</th>
                        <th>Remarks</th>
                        <th>Documents</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($verifications as $ver)
                        <tr>
                            <td><x-status-badge :status="$ver->status" /></td>
                            <td class="small text-muted">{{ $ver->created_at->format('M d, Y') }}</td>
                            <td class="small">{{ $ver->reviewer?->name ?? '—' }}</td>
                            <td class="small text-secondary">{{ $ver->remarks ?? '—' }}</td>
                            <td>
                                @if($ver->documents_url)
                                    <a href="{{ $ver->documents_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark-text me-1"></i> View Docs
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
