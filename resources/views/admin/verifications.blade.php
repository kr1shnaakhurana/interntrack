@extends('layouts.dashboard')

@section('title', 'Company Verifications - InternTrack Admin')
@section('page_title', 'Company Verification Queue')

@section('content')
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Company</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Submitted</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Status</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Documents</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Remarks</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Admin Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($verifications as $ver)
                    <tr>
                        <td class="px-4 py-4 align-middle">
                            <div class="fw-bold text-dark">{{ $ver->company->name ?? 'Unknown' }}</div>
                            <div class="text-muted small">{{ $ver->company->sector ?? '' }}</div>
                        </td>
                        <td class="px-4 py-4 align-middle small text-muted">{{ $ver->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-4 align-middle"><x-status-badge :status="$ver->status" /></td>
                        <td class="px-4 py-4 align-middle">
                            @if($ver->documents_url)
                                <a href="{{ $ver->documents_url }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-file-earmark-text me-1"></i> Review
                                </a>
                            @else
                                <span class="text-muted small">No doc</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 align-middle text-secondary small cell-narrow">{{ Str::limit($ver->remarks ?? '—', 60) }}</td>
                        <td class="px-4 py-4 align-middle">
                            @if($ver->status === 'pending')
                                <div class="d-flex gap-2">
                                    <form action="/admin/verifications/{{ $ver->id }}/approve" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-4">
                                            <i class="bi bi-check-circle me-1"></i> Approve
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-4"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $ver->id }}">
                                        <i class="bi bi-x-circle me-1"></i> Reject
                                    </button>
                                </div>

                                <div class="modal fade" id="rejectModal{{ $ver->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content rounded-4 border-0">
                                            <form action="/admin/verifications/{{ $ver->id }}/reject" method="POST">
                                                @csrf
                                                <div class="modal-header px-4 py-3 border-bottom border-light">
                                                    <h5 class="modal-title fw-bold text-dark">Reject Verification</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body px-4 py-3">
                                                    <p class="small text-muted">Company: <strong>{{ $ver->company->name ?? '' }}</strong></p>
                                                    <label class="form-label small fw-medium text-danger">Rejection Reason <span class="text-danger">*</span></label>
                                                    <textarea name="remarks" class="form-control form-control-sm rounded-3" rows="3" required placeholder="Briefly explain the rejection reason to the company..."></textarea>
                                                </div>
                                                <div class="modal-footer px-4 py-3 border-top border-light">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-4">Confirm Rejection</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">Reviewed by {{ $ver->reviewer?->name ?? 'Admin' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <x-empty-state title="No Verification Requests" message="No verification requests in queue." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        <x-pagination :paginator="$verifications" />
    </div>
</div>
@endsection