<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\JobSource;
use App\Services\Jobs\JobImportService;
use App\Services\Jobs\JobSourceManager;
use Exception;
use Illuminate\Http\Request;

class WebJobSourceController extends Controller
{
    public function __construct(
        protected JobSourceManager $sourceManager,
        protected JobImportService $importService
    ) {}

    /**
     * Display a listing of all configured job sources.
     */
    public function index()
    {
        $sources = JobSource::withCount('jobs')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        $drivers = $this->sourceManager->getAvailableDrivers();

        $stats = [
            'total' => JobSource::count(),
            'active' => JobSource::where('is_active', true)->count(),
            'external' => JobSource::where('type', 'external')->count(),
            'total_external_jobs' => \App\Models\Job::whereNotNull('source_id')->count(),
        ];

        return view('admin.sources.index', compact('sources', 'drivers', 'stats'));
    }

    /**
     * Store a newly created job source.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'adapter_key' => 'required|string|max:50',
            'type' => 'required|in:external,direct',
            'base_url' => 'nullable|url|max:255',
            'api_endpoint' => 'nullable|url|max:500',
            'auth_type' => 'required|in:none,api_key,bearer',
            'is_active' => 'nullable|boolean',
            'country' => 'nullable|string|max:10',
            'category' => 'nullable|string|max:50',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $config = [];
        if (! empty($validated['country'])) {
            $config['country'] = $validated['country'];
        }
        if (! empty($validated['category'])) {
            $config['category'] = $validated['category'];
        }
        if (! empty($validated['limit'])) {
            $config['limit'] = (int) $validated['limit'];
        }

        $source = JobSource::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'adapter_key' => $validated['adapter_key'],
            'base_url' => $validated['base_url'] ?? null,
            'api_endpoint' => $validated['api_endpoint'] ?? null,
            'auth_type' => $validated['auth_type'],
            'config' => ! empty($config) ? $config : null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $this->logAdminAction($request, 'create_job_source', 'JobSource', $source->id, "Created job source '{$source->name}' with adapter '{$source->adapter_key}'");

        return back()->with('success', "Job source '{$source->name}' added successfully.");
    }

    /**
     * Update an existing job source configuration.
     */
    public function update(Request $request, string $id)
    {
        $source = JobSource::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'adapter_key' => 'required|string|max:50',
            'base_url' => 'nullable|url|max:255',
            'api_endpoint' => 'nullable|url|max:500',
            'auth_type' => 'required|in:none,api_key,bearer',
            'is_active' => 'nullable|boolean',
        ]);

        $source->update([
            'name' => $validated['name'],
            'adapter_key' => $validated['adapter_key'],
            'base_url' => $validated['base_url'] ?? null,
            'api_endpoint' => $validated['api_endpoint'] ?? null,
            'auth_type' => $validated['auth_type'],
            'is_active' => $request->boolean('is_active', false),
        ]);

        $this->logAdminAction($request, 'update_job_source', 'JobSource', $source->id, "Updated job source '{$source->name}'");

        return back()->with('success', "Job source '{$source->name}' updated successfully.");
    }

    /**
     * Toggle active/inactive status of a job source.
     */
    public function toggle(Request $request, string $id)
    {
        $source = JobSource::findOrFail($id);
        $source->is_active = ! $source->is_active;
        $source->save();

        $statusStr = $source->is_active ? 'enabled' : 'disabled';
        $this->logAdminAction($request, 'toggle_job_source', 'JobSource', $source->id, "Changed status of '{$source->name}' to {$statusStr}");

        return back()->with('success', "Job source '{$source->name}' is now {$statusStr}.");
    }

    /**
     * Trigger manual synchronization for a specific source.
     */
    public function sync(Request $request, string $id)
    {
        $source = JobSource::findOrFail($id);

        if (! $source->is_active) {
            return back()->with('error', "Cannot synchronize '{$source->name}' because it is disabled.");
        }

        try {
            $metrics = $this->importService->importFromSource($source);

            $this->logAdminAction($request, 'sync_job_source', 'JobSource', $source->id, "Manual sync completed for '{$source->name}': {$metrics['inserted']} added, {$metrics['updated']} updated");

            return back()->with('success', "Manual sync completed for {$source->name}: {$metrics['inserted']} new opportunities imported, {$metrics['updated']} updated, {$metrics['rejected']} skipped in {$metrics['duration_ms']}ms.");
        } catch (Exception $e) {
            $this->logAdminAction($request, 'sync_job_source_failed', 'JobSource', $source->id, "Manual sync failed for '{$source->name}': " . $e->getMessage());

            return back()->with('error', "Sync failed for '{$source->name}': " . $e->getMessage());
        }
    }

    protected function logAdminAction(Request $request, string $action, string $entityType, string $entityId, ?string $description = null): void
    {
        $admin = $request->user()->admin;
        if ($admin) {
            AdminLog::create([
                'admin_id' => $admin->id,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
            ]);
        }
    }
}

