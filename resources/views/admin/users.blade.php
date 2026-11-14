@extends('layouts.dashboard')

@section('title', 'User Directory - InternTrack Admin')
@section('page_title', 'Platform User Directory')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <div>
        <p class="text-muted small mb-0">System-wide directory of all student, company, and administrative accounts.</p>
    </div>
    <div class="text-muted small">
        Showing <strong class="text-dark">{{ $users->total() }}</strong> total users
    </div>
</div>

<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Email Address</th>
                    <th>Account Role</th>
                    <th>Profile Association</th>
                    <th>Status</th>
                    <th class="text-end">Joined Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="fw-semibold text-dark">{{ $user->name }}</td>
                        <td class="small text-muted">{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'admin')
                                <span class="badge badge-subtle-danger">Admin</span>
                            @elseif($user->role === 'company')
                                <span class="badge badge-subtle-primary">Employer</span>
                            @else
                                <span class="badge badge-subtle-info">Student</span>
                            @endif
                        </td>
                        <td class="small text-secondary">
                            @if($user->student)
                                {{ $user->student->program ?? 'Student Candidate' }}
                            @elseif($user->company)
                                {{ $user->company->name }}
                                @if($user->company->latestVerification?->status === 'approved')
                                    <i class="bi bi-patch-check-fill text-primary ms-1" title="Verified Employer"></i>
                                @endif
                            @else
                                <span class="text-muted">Platform Staff</span>
                            @endif
                        </td>
                        <td>
                            <x-status-badge :status="$user->is_active ? 'active' : 'suspended'" />
                        </td>
                        <td class="small text-muted text-end">{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <x-empty-state title="No Users Found" message="There are currently no registered users in this directory." icon="bi-people" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$users" />
@endsection
