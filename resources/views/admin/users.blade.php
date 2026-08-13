@extends('layouts.dashboard')

@section('title', 'User Directory - InternTrack Admin')
@section('page_title', 'Platform User Directory')

@section('content')
<div class="card card-custom bg-white p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
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
                        <td class="fw-bold text-dark">{{ $user->name }}</td>
                        <td class="small text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : ($user->role === 'company' ? 'bg-dark' : 'bg-primary') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="small text-muted">
                            @if($user->student)
                                {{ $user->student->program ?? 'Student' }}
                            @elseif($user->company)
                                {{ $user->company->name }}
                                @if($user->company->latestVerification?->status === 'approved')
                                    <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
                                @endif
                            @else
                                Platform Admin
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
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
