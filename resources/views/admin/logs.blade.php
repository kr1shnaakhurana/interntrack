@extends('layouts.dashboard')

@section('title', 'Audit Logs - InternTrack Admin')
@section('page_title', 'System Audit Trail')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <div>
        <p class="text-muted small mb-0">Immutable record of administrator actions, approvals, rejects, and automated background jobs.</p>
    </div>
    <div class="text-muted small">
        Showing <strong class="text-dark">{{ $logs->total() }}</strong> total log entries
    </div>
</div>

<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Admin User</th>
                    <th>Action</th>
                    <th>Entity Type</th>
                    <th>Entity ID</th>
                    <th>Audit Details</th>
                    <th class="text-end">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="fw-semibold text-dark">{{ $log->admin->user->name ?? 'System Automated' }}</td>
                        <td>
                            <span class="badge badge-subtle-primary">
                                {{ str_replace('_', ' ', strtoupper($log->action)) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $log->entity_type ?? '—' }}</td>
                        <td class="small text-muted">#{{ $log->entity_id ?? '—' }}</td>
                        <td class="small text-secondary" style="max-width: 300px;">
                            <span class="text-truncate d-inline-block w-100" title="{{ $log->details }}">{{ $log->details ?? '—' }}</span>
                        </td>
                        <td class="small text-muted text-end">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <x-empty-state title="No Audit Records" message="Administrative actions will appear in this audit log." icon="bi-journal-text" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$logs" />
@endsection
