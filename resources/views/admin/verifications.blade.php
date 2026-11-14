@extends('layouts.dashboard')

@section('title', 'Company Verifications - InternTrack Admin')
@section('page_title', 'Employer Verification Queue')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <div>
        <p class="text-muted small mb-0">Evaluate organization verification requests, examine evidence documents, and grant direct posting rights.</p>
    </div>
    <div class="text-muted small">
        Showing <strong class="text-dark">{{ $verifications->total() }}</strong> total requests
    </div>
</div>

<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Organization</th>
                    <th>Submitted Date</th>
                    <th>Status</th>
                    <th>Evidence Link</th>
                    <th>Remarks</th>
                    <th class="text-end">Verification Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($verifications as $ver)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $ver->company->name ?? 'Unknown' }}</div>
                            <div class="text-muted small text-capitalize">{{ $ver->company->sector ?? 'Private' }}</div>
                        </td>
                        <td class="small text-muted">{{ $ver->created_at->format('M d, Y') }}</td>
                        <td><x-status-badge :status="$ver->status" /></td>
                        <td>
                            @if($ver->documents_url)
                                <a href="{{ $ver->documents_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-file-earmark-text"></i> Review Docs
                                </a>
                            @else
                                <span class="text-muted small">No document</span>
                            @endif
                        </td>
                        <td class="text-secondary small" style="max-width: 220px;">
                            <span class="text-truncate d-inline-block w-100" title="{{ $ver->remarks }}">{{ $ver->remarks ?? '—' }}</span>
                        </td>
                        <td class="text-end">
                            @if($ver->status === 'pending')
                                <div class="d-inline-flex align-items-center gap-1">
                                    <form action="/admin/verifications/{{ $ver->id }}/approve" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $ver->id }}">
                                        <i class="bi bi-x-lg"></i> Reject
                                    </button>
                                </div>

                                <!-- Rejection Modal Dialog -->
                                <div class="modal fade" id="rejectModal{{ $ver->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $ver->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content">
                                            <form action="/admin/verifications/{{ $ver->id }}/reject" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="rejectModalLabel{{ $ver->id }}">Reject Employer Verification</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <div class="text-muted small">Organization: <strong class="text-dark">{{ $ver->company->name ?? '' }}</strong></div>
                                                    </div>
                                                    <div class="mb-1">
                                                        <label for="remarks_{{ $ver->id }}" class="form-label text-danger">Rejection Reason <span class="text-danger">*</span></label>
                                                        <textarea id="remarks_{{ $ver->id }}" name="remarks" class="form-control form-control-sm" rows="3" required placeholder="Specify why the documents were rejected so the employer can rectify..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger btn-sm">Confirm Rejection</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">
                                    Reviewed by <strong class="text-dark">{{ $ver->reviewer?->name ?? 'Admin' }}</strong>
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <x-empty-state title="Verification Queue Empty" message="There are currently no employer verification requests pending review." icon="bi-patch-check" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$verifications" />
@endsection