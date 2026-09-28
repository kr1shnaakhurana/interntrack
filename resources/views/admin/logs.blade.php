@extends('layouts.dashboard')

@section('title', 'Audit Logs - InternTrack Admin')
@section('page_title', 'Admin Audit Logs')

@section('content')
<div class="bg-white rounded-xl p-3 shadow-sm border border-gray-200">
    <div class="table-responsive">
        <table class="w-full rounded-lg overflow-auto border border-gray-200">
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
                        <td class="font-semibold text-gray-900">{{ $log->admin->user->name ?? 'System' }}</td>
                        <td>
                            <span class="badge bg-indigo-100 text-indigo-800 rounded-pill px-2 border border-indigo-100">
                                {{ str_replace('_', ' ', strtoupper($log->action)) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $log->entity_type ?? '?' }}</td>
                        <td class="small text-muted">{{ $log->entity_id ?? '?' }}</td>
                        <td class="small text-gray-500 cell-medium">
                            {{ Str::limit($log->details ?? '?', 80) }}
                        </td>
                        <td class="text-muted small">{{ $log->created_at->format('M d, Y h:i A') }}</td>
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

