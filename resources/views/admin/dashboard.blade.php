@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - InternTrack')
@section('page_title', 'Admin Overview')

@section('content')
<!-- Platform Stats -->
<div class="row g-3 mb-5">
    <div class="col-md-2">
        <div class="card card-stat p-3 bg-white border-start border-primary border-4">
            <div class="text-muted small fw-semibold">TOTAL USERS</div>
            <div class="display-6 fw-bold text-dark">{{ $totalUsers }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card card-stat p-3 bg-white border-start border-info border-4">
            <div class="text-muted small fw-semibold">STUDENTS</div>
            <div class="display-6 fw-bold text-dark">{{ $totalStudents }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card card-stat p-3 bg-white border-start border-secondary border-4">
            <div class="text-muted small fw-semibold">COMPANIES</div>
            <div class="display-6 fw-bold text-dark">{{ $totalCompanies }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card card-stat p-3 bg-white border-start border-warning border-4">
            <div class="text-muted small fw-semibold">PENDING VERIFICATIONS</div>
            <div class="display-6 fw-bold text-dark">{{ $pendingVerificationsCount }}</div>
            <a href="/admin/verifications" class="small text-warning mt-1 d-inline-block">Review Now &rarr;</a>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card card-stat p-3 bg-white border-start border-success border-4">
            <div class="text-muted small fw-semibold">TOTAL JOBS</div>
            <div class="display-6 fw-bold text-dark">{{ $totalJobs }}</div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card card-stat p-3 bg-white border-start border-dark border-4">
            <div class="text-muted small fw-semibold">EXTERNAL JOBS</div>
            <div class="display-6 fw-bold text-dark">{{ $externalJobsCount }}</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-5">
    <div class="col-md-3">
        <a href="/admin/verifications" class="card card-custom bg-white p-3 text-decoration-none d-flex flex-row align-items-center gap-3">
            <i class="bi bi-patch-check fs-3 text-warning"></i>
            <div>
                <div class="fw-bold text-dark">Company Approvals</div>
                <div class="small text-muted">Review pending verifications</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="/admin/jobs" class="card card-custom bg-white p-3 text-decoration-none d-flex flex-row align-items-center gap-3">
            <i class="bi bi-list-stars fs-3 text-primary"></i>
            <div>
                <div class="fw-bold text-dark">All Job Listings</div>
                <div class="small text-muted">Monitor all job postings</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="/admin/users" class="card card-custom bg-white p-3 text-decoration-none d-flex flex-row align-items-center gap-3">
            <i class="bi bi-people-fill fs-3 text-success"></i>
            <div>
                <div class="fw-bold text-dark">User Directory</div>
                <div class="small text-muted">View all platform users</div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <form action="/admin/aggregator" method="POST">
            @csrf
            <button type="submit" class="card card-custom bg-white p-3 text-decoration-none d-flex flex-row align-items-center gap-3 w-100 border-0 text-start" style="cursor:pointer;">
                <i class="bi bi-cloud-download fs-3 text-info"></i>
                <div>
                    <div class="fw-bold text-dark">Run Job Aggregator</div>
                    <div class="small text-muted">Fetch external job feeds</div>
                </div>
            </button>
        </form>
    </div>
</div>

<!-- Recent Audit Logs -->
<h5 class="fw-bold mb-3">Recent Admin Activity</h5>
<div class="card card-custom bg-white p-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Admin</th>
                    <th>Action</th>
                    <th>Target Entity</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                    <tr>
                        <td class="fw-semibold">{{ $log->admin->user->name ?? 'System' }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ str_replace('_', ' ', strtoupper($log->action)) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $log->entity_type ?? '—' }} #{{ $log->entity_id ?? '—' }}</td>
                        <td class="small text-muted">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">No admin activity logged yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-2 text-end">
        <a href="/admin/logs" class="small text-primary text-decoration-none">View Full Audit Log &rarr;</a>
    </div>
</div>
@endsection
