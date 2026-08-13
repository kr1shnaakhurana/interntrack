@extends('layouts.dashboard')

@section('title', 'Company Verifications - InternTrack Admin')
@section('page_title', 'Company Verification Queue')

@section('content')
<div class="card card-custom bg-white p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Company</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th>Remarks</th>
                    <th>Admin Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($verifications as $ver)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $ver->company->name ?? 'Unknown' }}</div>
                            <div class="small text-muted">{{ $ver->company->sector ?? '' }}</div>
                        </td>
                        <td class="small text-muted">{{ $ver->created_at->format('M d, Y') }}</td>
                        <td><x-status-badge :status="$ver->status" /></td>
                        <td>
                            @if($ver->documents_url)
                                <a href="{{ $ver->documents_url }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-file-earmark-text me-1"></i> Review
                                </a>
                            @else
                                <span class="text-muted small">No doc</span>
                            @endif
                        </td>
                        <td class="small text-secondary" style="max-width: 200px;">
                            {{ Str::limit($ver->remarks ?? '—', 60) }}
                        </td>
                        <td>
                            @if($ver->status === 'pending')
                                <div class="d-flex gap-1">
                                    <form action="/admin/verifications/{{ $ver->id }}/approve" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm fw-semibold"
                                            onclick="return confirm('Approve verification for {{ addslashes($ver->company->name ?? '') }}?')">
                                            <i class="bi bi-check-circle me-1"></i> Approve
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger btn-sm fw-semibold"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $ver->id }}">
                                        <i class="bi bi-x-circle me-1"></i> Reject
                                    </button>
                                </div>

                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $ver->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="/admin/verifications/{{ $ver->id }}/reject" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Reject Verification</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="small text-muted">Company: <strong>{{ $ver->company->name ?? '' }}</strong></p>
                                                    <label class="form-label fw-semibold small">Rejection Reason <span class="text-danger">*</span></label>
                                                    <textarea name="remarks" class="form-control" rows="3" required
                                                        placeholder="Briefly explain the rejection reason to the company..."></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger btn-sm fw-semibold">Confirm Rejection</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">
                                    Reviewed by {{ $ver->reviewer?->name ?? 'Admin' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <x-empty-state title="No Verification Requests" message="No verification requests in queue." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$verifications" />
</div>
@endsection
