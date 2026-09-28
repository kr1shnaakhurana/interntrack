@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - InternTrack')
@section('page_title', 'Admin Overview')

@section('content')
<div class="row g-3 mb-5">
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Users</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $totalUsers }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Students</span>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                        <i class="bi bi-mortarboard fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $totalStudents }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Companies</span>
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 p-2">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $totalCompanies }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Pending Verifications</span>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                        <i class="bi bi-shield-exclamation fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $pendingVerificationsCount }}</div>
                <a href="/admin/verifications" class="small text-warning text-decoration-none fw-medium d-inline-block">Review Now &rarr;</a>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">Total Jobs</span>
                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                        <i class="bi bi-briefcase fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $totalJobs }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small fw-semibold text-uppercase">External Jobs</span>
                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 p-2">
                        <i class="bi bi-globe fs-5"></i>
                    </div>
                </div>
                <div class="display-6 fw-bold text-dark mb-2">{{ $externalJobsCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-5">
    <div class="col-12 col-md-6 col-lg-3">
        <a href="/admin/verifications" class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden text-decoration-none d-block">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="bi bi-patch-check fs-3"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Company Approvals</div>
                    <div class="text-muted small">Review pending verifications</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <a href="/admin/jobs" class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden text-decoration-none d-block">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="bi bi-list-stars fs-3"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">All Job Listings</div>
                    <div class="text-muted small">Monitor all job postings</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <a href="/admin/users" class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden text-decoration-none d-block">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">User Directory</div>
                    <div class="text-muted small">View all platform users</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <form action="/admin/aggregator" method="POST">
            @csrf
            <button type="submit" class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden w-100 text-start p-0 clickable">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                        <i class="bi bi-cloud-download fs-3"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Run Job Aggregator</div>
                        <div class="text-muted small">Fetch external job feeds</div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<h5 class="fw-bold text-secondary mb-4">Recent Admin Activity</h5>
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Admin</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Action</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Target Entity</th>
                    <th class="fw-semibold text-secondary small text-uppercase px-4 py-3">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                    <tr>
                        <td class="px-4 py-4 align-middle fw-semibold text-dark">{{ $log->admin->user->name ?? 'System' }}</td>
                        <td class="px-4 py-4 align-middle">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 small">{{ str_replace('_', ' ', strtoupper($log->action)) }}</span>
                        </td>
                        <td class="px-4 py-4 align-middle small text-muted">{{ $log->entity_type ?? '—' }}</td>
                        <td class="px-4 py-4 align-middle small text-muted">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">No admin activity logged yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 py-3 text-end">
        <a href="/admin/logs" class="text-primary text-decoration-none fw-medium">View Full Audit Log &rarr;</a>
    </div>
</div>
@endsection