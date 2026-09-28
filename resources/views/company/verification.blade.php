@extends('layouts.dashboard')

@section('title', 'Organisation Verification - InternTrack')

@section('content')
<div class="page-header">
    <h1 class="page-title">Organisation Verification</h1>
</div>

@php
    $latest = $company->latestVerification ?? null;
    $status = $latest?->status ?? 'not_submitted';
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex align-items-center gap-3">
            @if($status === 'approved')
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="bi bi-patch-check-fill fs-2"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-success mb-1">Verified Organisation</h5>
                    <p class="mb-0 text-muted small">Your organisation is verified. You may publish direct job opportunities.</p>
                </div>
            @elseif($status === 'pending')
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="bi bi-hourglass-split fs-2"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-warning mb-1">Verification Under Review</h5>
                    <p class="mb-0 text-muted small">Submitted on {{ $latest?->created_at?->format('M d, Y') }}. An admin will review within 2-3 business days.</p>
                </div>
            @elseif($status === 'rejected')
                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                    <i class="bi bi-x-circle-fill fs-2"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-danger mb-1">Verification Rejected</h5>
                    <p class="mb-0 text-muted small">Reason: {{ $latest?->remarks ?? 'See history below.' }}</p>
                    <small class="text-muted">You may resubmit with updated documentation.</small>
                </div>
            @else
                <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 p-3">
                    <i class="bi bi-shield-exclamation fs-2"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-secondary mb-1">Not Yet Verified</h5>
                    <p class="mb-0 text-muted small">Submit a verification request to unlock job publishing.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($status !== 'approved' && $status !== 'pending')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4 p-md-5">
            <h5 class="fw-bold text-secondary mb-4"><i class="bi bi-send text-primary me-2"></i>Submit Verification Request</h5>
            <form action="/company/verification" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="documents_url" class="form-label small fw-medium text-secondary">Document / Evidence URL</label>
                    <input type="url" name="documents_url" id="documents_url" class="form-control form-control-sm rounded-3" required placeholder="https://drive.google.com/file/... or https://storage.com/cert.pdf">
                    <div class="form-text text-muted">Link to your registration certificate, GST document, or official letter.</div>
                </div>
                <div class="mb-4">
                    <label for="remarks" class="form-label small fw-medium text-secondary">Additional Remarks (Optional)</label>
                    <textarea name="remarks" id="remarks" class="form-control form-control-sm rounded-3" rows="3" placeholder="Briefly describe your organisation and the attached documents..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2">
                    <i class="bi bi-send me-1"></i> Submit for Review
                </button>
            </form>
        </div>
    </div>
@endif

@if($verifications->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5">
            <h5 class="fw-bold text-secondary mb-4">Verification History</h5>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Status</th>
                            <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Submitted</th>
                            <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Reviewed By</th>
                            <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Remarks</th>
                            <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Documents</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($verifications as $ver)
                            <tr>
                                <td class="px-4 py-4"><x-status-badge :status="$ver->status" /></td>
                                <td class="px-4 py-4 small text-muted">{{ $ver->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-4 small">{{ $ver->reviewer?->name ?? '—' }}</td>
                                <td class="px-4 py-4 small text-secondary">{{ $ver->remarks ?? '—' }}</td>
                                <td class="px-4 py-4">
                                    @if($ver->documents_url)
                                        <a href="{{ $ver->documents_url }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="bi bi-file-earmark-text me-1"></i> View Docs
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection