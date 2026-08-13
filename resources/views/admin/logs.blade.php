@extends('layouts.dashboard')

@section('title', 'Audit Logs - InternTrack Admin')
@section('page_title', 'Admin Audit Logs')

@section('content')
<div class="card card-custom bg-white p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Entity Type</th>
                    <th>Entity ID</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="fw-semibold">{{ $log->admin->user->name ?? 'System' }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2">
                                {{ str_replace('_', ' ', strtoupper($log->action)) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $log->entity_type ?? '—' }}</td>
                        <td class="small text-muted">{{ $log->entity_id ?? '—' }}</td>
                        <td class="small text-secondary" style="max-width: 250px;">
                            {{ Str::limit($log->details ?? '—', 80) }}
                        </td>
                        <td class="small text-muted">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <x-empty-state title="No Audit Logs" message="Admin actions will be recorded here." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$logs" />
</div>
@endsection
