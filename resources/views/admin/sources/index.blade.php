@extends('layouts.dashboard')

@section('title', 'External Job Sources - InternTrack Admin')
@section('page_title', 'External Job & Government Feeds Management')

@section('content')
<!-- Header Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Configured Sources</div>
                <div class="stat-metric-value">{{ $stats['total'] }}</div>
            </div>
            <span class="text-muted small">Registered adapters</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Active Sources</div>
                <div class="stat-metric-value text-success">{{ $stats['active'] }}</div>
            </div>
            <span class="text-success small"><i class="bi bi-check-circle me-1"></i>Scheduled for sync</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">External Feeds</div>
                <div class="stat-metric-value text-primary">{{ $stats['external'] }}</div>
            </div>
            <span class="text-muted small">REST APIs &amp; RSS</span>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-metric-card h-100">
            <div>
                <div class="stat-metric-label">Aggregated Jobs</div>
                <div class="stat-metric-value text-dark">{{ $stats['total_external_jobs'] }}</div>
            </div>
            <span class="text-muted small">Imported into directory</span>
        </div>
    </div>
</div>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <div>
        <p class="text-muted small mb-0">Manage authorized external job APIs, government RSS feeds, and data normalization adapters.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addSourceModal">
            <i class="bi bi-plus-lg"></i> Add Job Source
        </button>
    </div>
</div>

<!-- Sources Table -->
<div class="table-container mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Source Name &amp; Adapter</th>
                    <th>Type &amp; Auth</th>
                    <th>Endpoint / Base URL</th>
                    <th>Postings</th>
                    <th>Last Fetch</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sources as $source)
                    <tr>
                        <td>
                            <div class="fw-semibold text-dark">{{ $source->name }}</div>
                            <div class="d-flex align-items-center gap-1 mt-0.5">
                                <span class="badge badge-subtle-primary" style="font-size: 10.5px;">
                                    {{ $source->adapter_key ?? 'standard' }}
                                </span>
                                @if(str_contains(strtolower($source->name), 'gov') || $source->adapter_key === 'government_rss')
                                    <span class="badge badge-subtle-success" style="font-size: 10.5px;">Govt Recruitment</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="small text-secondary text-capitalize">{{ $source->type }}</div>
                            <span class="text-muted" style="font-size: 11px;">
                                Auth: <strong class="text-dark">{{ strtoupper($source->auth_type ?? 'NONE') }}</strong>
                            </span>
                        </td>
                        <td style="max-width: 250px;">
                            @if($source->api_endpoint || $source->base_url)
                                <div class="text-truncate small">
                                    <a href="{{ $source->api_endpoint ?: $source->base_url }}" target="_blank" rel="noopener noreferrer" class="text-muted text-decoration-none" title="{{ $source->api_endpoint ?: $source->base_url }}">
                                        <i class="bi bi-link-45deg me-1"></i>{{ $source->api_endpoint ?: $source->base_url }}
                                    </a>
                                </div>
                            @else
                                <span class="text-muted small">Internal / Direct Feed</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-subtle-neutral">
                                {{ $source->jobs_count ?? $source->jobs->count() }} opportunities
                            </span>
                        </td>
                        <td>
                            @if($source->last_fetched_at)
                                <div class="small text-dark fw-medium">{{ $source->last_fetched_at->format('M d, H:i') }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $source->last_fetched_at->diffForHumans() }}</div>
                            @else
                                <span class="text-muted small">Never synced</span>
                            @endif

                            @if($source->last_fetch_status === 'failed')
                                <div class="badge badge-subtle-danger mt-1 text-truncate" style="max-width: 140px;" title="{{ $source->last_fetch_error }}">
                                    <i class="bi bi-exclamation-triangle me-1"></i>{{ Str::limit($source->last_fetch_error, 20) }}
                                </div>
                            @elseif($source->last_fetch_status === 'success')
                                <div class="badge badge-subtle-success mt-1">
                                    <i class="bi bi-check-circle me-1"></i>Success
                                </div>
                                @if(!empty($source->config['last_metrics']))
                                    <div class="text-muted mt-1" style="font-size: 10px;">
                                        +{{ $source->config['last_metrics']['inserted'] ?? 0 }} new,
                                        {{ $source->config['last_metrics']['updated'] ?? 0 }} upd
                                        @if(($source->config['last_metrics']['skipped_stale'] ?? 0) > 0)
                                            , {{ $source->config['last_metrics']['skipped_stale'] }} stale
                                        @endif
                                        @if(($source->config['last_metrics']['rejected'] ?? 0) > 0)
                                            , {{ $source->config['last_metrics']['rejected'] }} invalid
                                        @endif
                                    </div>
                                @endif
                            @elseif($source->last_fetch_status === 'running')
                                <div class="badge badge-subtle-primary mt-1">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>Syncing
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($source->is_active)
                                <span class="badge badge-subtle-success"><i class="bi bi-dot"></i> Active</span>
                            @else
                                <span class="badge badge-subtle-neutral"><i class="bi bi-dot"></i> Disabled</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                @if($source->is_active)
                                    <form action="/admin/job-sources/{{ $source->id }}/sync" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary btn-sm py-1 px-2" title="Trigger Manual Sync">
                                            <i class="bi bi-arrow-repeat"></i> Sync
                                        </button>
                                    </form>
                                @endif

                                <form action="/admin/job-sources/{{ $source->id }}/toggle" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary btn-sm py-1 px-2" title="{{ $source->is_active ? 'Disable Source' : 'Enable Source' }}">
                                        @if($source->is_active)
                                            <i class="bi bi-pause"></i> Disable
                                        @else
                                            <i class="bi bi-play"></i> Enable
                                        @endif
                                    </button>
                                </form>

                                <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2" data-bs-toggle="modal" data-bs-target="#editModal{{ str_replace('-', '', $source->id) }}" title="Edit Settings">
                                    <i class="bi bi-gear"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Source Modal -->
                    <div class="modal fade" id="editModal{{ str_replace('-', '', $source->id) }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="/admin/job-sources/{{ $source->id }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fs-6">Edit Source: {{ $source->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Source Name</label>
                                            <input type="text" name="name" class="form-control form-control-sm" value="{{ $source->name }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Adapter Driver</label>
                                            <select name="adapter_key" class="form-select form-select-sm" required>
                                                @foreach($drivers as $driverKey => $driver)
                                                    <option value="{{ $driverKey }}" {{ $source->adapter_key === $driverKey ? 'selected' : '' }}>
                                                        {{ $driver['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">API Endpoint / Feed URL</label>
                                            <input type="url" name="api_endpoint" class="form-control form-control-sm" value="{{ $source->api_endpoint }}" placeholder="https://api.example.com/v1/jobs">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Base Website URL</label>
                                            <input type="url" name="base_url" class="form-control form-control-sm" value="{{ $source->base_url }}" placeholder="https://example.com">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Authentication Type</label>
                                            <select name="auth_type" class="form-select form-select-sm">
                                                <option value="none" {{ $source->auth_type === 'none' ? 'selected' : '' }}>None (Public Feed / Unauthenticated)</option>
                                                <option value="api_key" {{ $source->auth_type === 'api_key' ? 'selected' : '' }}>API Key (from .env)</option>
                                                <option value="bearer" {{ $source->auth_type === 'bearer' ? 'selected' : '' }}>Bearer Token (from .env)</option>
                                            </select>
                                            <div class="form-text text-muted" style="font-size: 11px;">
                                                <i class="bi bi-shield-lock me-1"></i>API keys and secret tokens are loaded securely from environment variables and never stored in the database.
                                            </div>
                                        </div>

                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeToggle{{ $source->id }}" {{ $source->is_active ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="activeToggle{{ $source->id }}">Enable active scheduled synchronization</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer py-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <x-empty-state title="No Job Sources Configured" message="Add an external job API or government RSS feed to begin aggregating career opportunities." icon="bi-cloud-arrow-down" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<x-pagination :paginator="$sources" />

<!-- Add Job Source Modal -->
<div class="modal fade" id="addSourceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="/admin/job-sources" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fs-6">Add New Job Source</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Source Display Name</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. UPSC Official Recruitment Feed" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Adapter Driver</label>
                        <select name="adapter_key" id="adapterSelect" class="form-select form-select-sm" required>
                            @foreach($drivers as $key => $driver)
                                <option value="{{ $key }}" data-auth="{{ $driver['auth_type'] }}">
                                    {{ $driver['name'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted" id="driverHelpText" style="font-size: 11px;">
                            Choose an adapter specialized for the target platform.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Feed or API Endpoint URL</label>
                        <input type="url" name="api_endpoint" class="form-control form-control-sm" placeholder="https://example.gov.in/recruitment.xml">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Organization / Base Website URL</label>
                        <input type="url" name="base_url" class="form-control form-control-sm" placeholder="https://example.gov.in">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Source Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="external">External Feed</option>
                                <option value="direct">Direct Employer</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Authentication</label>
                            <select name="auth_type" class="form-select form-select-sm">
                                <option value="none">None (Public)</option>
                                <option value="api_key">API Key (.env)</option>
                                <option value="bearer">Bearer (.env)</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-2 bg-light rounded small text-muted mb-3" style="font-size: 11px;">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        <strong>Security Protection:</strong> InternTrack manages private credentials through Laravel environment variables (e.g. <code>ADZUNA_APP_KEY</code>). Secrets are never written to the PostgreSQL database or rendered in the DOM.
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="newSourceActive" checked>
                        <label class="form-check-label small" for="newSourceActive">Enable active automated synchronization</label>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create Job Source</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

