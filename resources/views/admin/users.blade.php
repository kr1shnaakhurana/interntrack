@extends('layouts.dashboard')

@section('title', 'User Directory - InternTrack Admin')
@section('page_title', 'Platform User Directory')

@section('content')
<div class="bg-white rounded-xl p-3 shadow-sm border border-gray-200">
    <div class="table-responsive">
        <table class="w-full rounded-lg overflow-auto border border-gray-200">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Profile Info</th>
                    <th>Status</th>
                    <th>Joined</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="font-bold text-gray-900">{{ $user->name }}</td>
                        <td class="small text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-red-600' : ($user->role === 'company' ? 'bg-indigo-500 text-white' : 'bg-indigo-600') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            @if($user->student)
                                {{ $user->student->program ?? 'Student' }}
                            @elseif($user->company)
                                {{ $user->company->name }}
                                @if($user->company->latestVerification?->status === 'approved')
                                    <i class="bi bi-patch-check-fill text-indigo-500 ms-1" title="Verified"></i>
                                @endif
                            @else
                                Platform Admin
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-indigo-100 text-indigo-800 rounded-pill' : 'bg-red-600 rounded-pill' }}">
                                {{ $user->is_active ? 'Active' : 'Suspended' }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-pagination :paginator="$users" />
</div>
@endsection




