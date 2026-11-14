@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - InternTrack')
@section('page_title', 'Administrative Overview')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Total Users</div>
                <div class="stat-metric-value">{{ $totalUsers }}</div>
            </div>
            <a href="/admin/users" class="stat-metric-link">Manage users &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Students</div>
                <div class="stat-metric-value">{{ $totalStudents }}</div>
            </div>
            <span class="text-muted small">Registered talent</span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Companies</div>
                <div class="stat-metric-value">{{ $totalCompanies }}</div>
            </div>
            <a href="/admin/companies" class="stat-metric-link">All companies &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Pending Review</div>
                <div class="stat-metric-value text-warning">{{ $pendingVerificationsCount }}</div>
            </div>
            <a href="/admin/verifications" class="stat-metric-link text-warning">Review queue &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Total Jobs</div>
                <div class="stat-metric-value">{{ $totalJobs }}</div>
            </div>
            <a href="/admin/jobs" class="stat-metric-link">All listings &rarr;</a>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">External Feeds</div>
                <div class="stat-metric-value">{{ $externalJobsCount }}</div>
            </div>
            <a href="/admin/job-sources" class="stat-metric-link">Manage feeds &rarr;</a>
        </div>
    </div>
</div>

<!-- Quick Administration Controls -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-lg-3">
        <a href="/admin/verifications" class="card text-decoration-none h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 38px; height: 38px; border-radius: 4px;">
                    <i class="bi bi-patch-check fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark small">Company Approvals</div>
                    <div class="text-muted" style="font-size: 11.5px;">Review pending verification requests</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <a href="/admin/jobs" class="card text-decoration-none h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 38px; height: 38px; border-radius: 4px;">
                    <i class="bi bi-list-stars fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark small">All Job Listings</div>
                    <div class="text-muted" style="font-size: 11.5px;">Manage direct and external postings</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <a href="/admin/users" class="card text-decoration-none h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 38px; height: 38px; border-radius: 4px;">
                    <i class="bi bi-people fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark small">User Directory</div>
                    <div class="text-muted" style="font-size: 11.5px;">View and audit platform accounts</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <form action="/admin/aggregator" method="POST" class="h-100 m-0">
            @csrf
            <button type="submit" class="card h-100 w-100 text-start p-0 border-0 bg-transparent" style="cursor: pointer;">
                <div class="card-body p-3 d-flex align-items-center gap-3 card">
                    <div class="d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info" style="width: 38px; height: 38px; border-radius: 4px;">
                        <i class="bi bi-cloud-arrow-down fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-semibold text-dark small">Run Job Aggregator</div>
                        <div class="text-muted" style="font-size: 11.5px;">Fetch external job feeds</div>
                    </div>
                </div>
            </button>
        </form>
    </div>
</div>

<!-- Recent Admin Audit Activity -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="section-title mb-0">Recent Admin Activity</h2>
        <p class="text-muted small mb-0">System audit log of recent staff and automated platform actions.</p>
    </div>
    <a href="/admin/logs" class="btn btn-outline-secondary btn-sm">View Full Log</a>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Administrator</th>
                    <th>Action</th>
                    <th>Target Entity</th>
                    <th class="text-end">Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLogs as $log)
                    <tr>
                        <td class="fw-semibold text-dark">{{ $log->admin->user->name ?? 'System Automated' }}</td>
                        <td>
                            <span class="badge badge-subtle-primary">
                                {{ str_replace('_', ' ', strtoupper($log->action)) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $log->entity_type ?? '—' }}</td>
                        <td class="small text-muted text-end">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <x-empty-state title="No Activity Logged" message="Administrative actions will appear in the system audit trail here." icon="bi-journal-text" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection