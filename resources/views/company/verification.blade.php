@extends('layouts.dashboard')

@section('title', 'Employer Verification - InternTrack')
@section('page_title', 'Organization Verification')

@section('content')
<div class="mb-3">
    <p class="text-muted small mb-0">Submit organization credentials and legal entity documents to gain verified employer status.</p>
</div>

@php
    $latest = $company->latestVerification ?? null;
    $status = $latest?->status ?? 'not_submitted';
@endphp

<!-- Verification Status Panel -->
<div class="card mb-4">
    <div class="card-header py-3">
        <span class="fw-semibold fs-6 text-dark">Current Verification Status</span>
    </div>
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3">
            @if($status === 'approved')
                <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 40px; height: 40px; border-radius: 4px;">
                    <i class="bi bi-patch-check-fill fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold text-success fs-6">Verified Organization</div>
                    <p class="mb-0 text-muted small">Your organization profile has been officially approved. You can post direct opportunities to candidates.</p>
                </div>
            @elseif($status === 'pending')
                <div class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 40px; height: 40px; border-radius: 4px;">
                    <i class="bi bi-hourglass-split fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold text-warning fs-6">Verification Under Review</div>
                    <p class="mb-0 text-muted small">Submitted on {{ $latest?->created_at?->format('M d, Y') }}. Our administrative team reviews submissions within 2 business days.</p>
                </div>
            @elseif($status === 'rejected')
                <div class="d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" style="width: 40px; height: 40px; border-radius: 4px;">
                    <i class="bi bi-x-circle-fill fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold text-danger fs-6">Verification Rejected</div>
                    <p class="mb-0 text-muted small">Administrator remarks: {{ $latest?->remarks ?? 'Please review documentation.' }}</p>
                    <small class="text-secondary">You may resubmit below with rectified documentation.</small>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary" style="width: 40px; height: 40px; border-radius: 4px;">
                    <i class="bi bi-shield-lock fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark fs-6">Unverified Profile</div>
                    <p class="mb-0 text-muted small">Complete the submission form below to unlock direct job publishing on InternTrack.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($status !== 'approved' && $status !== 'pending')
    <div class="card mb-4">
        <div class="card-header py-3">
            <span class="fw-semibold fs-6 text-dark">Submit Verification Request</span>
        </div>
        <div class="card-body p-4">
            <form action="/company/verification" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="documents_url" class="form-label">Supporting Document / Evidence Link <span class="text-danger">*</span></label>
                    <input type="url" name="documents_url" id="documents_url" class="form-control form-control-sm" required placeholder="https://drive.google.com/file/... or https://company.com/reg-doc.pdf">
                    <div class="text-muted" style="font-size: 11.5px;">Link to your business registration, incorporation certificate, or official university partnership letter.</div>
                </div>
                <div class="mb-3">
                    <label for="remarks" class="form-label">Additional Explanatory Remarks</label>
                    <textarea name="remarks" id="remarks" class="form-control form-control-sm" rows="3" placeholder="Provide any additional context or contact references..."></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-send"></i> Submit Verification
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@if($verifications->count() > 0)
    <div class="card">
        <div class="card-header py-3">
            <span class="fw-semibold fs-6 text-dark">Verification Audit History</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Submitted Date</th>
                        <th>Reviewed By</th>
                        <th>Admin Remarks</th>
                        <th class="text-end">Submitted Link</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($verifications as $ver)
                        <tr>
                            <td><x-status-badge :status="$ver->status" /></td>
                            <td class="small text-muted">{{ $ver->created_at->format('M d, Y') }}</td>
                            <td class="small text-dark">{{ $ver->reviewer?->name ?? '—' }}</td>
                            <td class="small text-secondary">{{ $ver->remarks ?? '—' }}</td>
                            <td class="text-end">
                                @if($ver->documents_url)
                                    <a href="{{ $ver->documents_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-box-arrow-up-right"></i> View Document
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
@endif
@endsection