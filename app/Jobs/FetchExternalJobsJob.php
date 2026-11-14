<?php

namespace App\Jobs;

use App\Models\JobSource;
use App\Services\Jobs\JobImportService;
use App\Services\Jobs\JobSourceManager;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchExternalJobsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 180;

    /**
     * Create a new job instance.
     *
     * @param string|JobSource $source
     * @param array $options
     */
    public function __construct(
        public string|JobSource $source = 'sample_external_feed',
        public array $options = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(JobSourceManager $sourceManager, JobImportService $importService): void
    {
        if (is_string($this->source)) {
            $q = JobSource::query();
            if (\Illuminate\Support\Str::isUuid($this->source)) {
                $q->where('id', $this->source);
            } else {
                $q->where(function ($sub) {
                    $sub->where('adapter_key', $this->source)
                        ->orWhere('name', $this->source);
                });
            }
            $jobSource = $q->first();
        } else {
            $jobSource = $this->source;
        }

        if (! $jobSource) {
            if (is_string($this->source)) {
                $jobSource = JobSource::firstOrCreate(
                    ['name' => $this->source],
                    ['type' => 'external', 'adapter_key' => $this->source, 'is_active' => true]
                );
            } else {
                Log::error("FetchExternalJobsJob: Source not found.");
                return;
            }
        }

        if (! $jobSource->is_active) {
            Log::info("FetchExternalJobsJob: Source '{$jobSource->name}' is disabled. Skipping.");
            return;
        }

        Log::info("Executing FetchExternalJobsJob for source '{$jobSource->name}'");

        try {
            $metrics = $importService->importFromSource($jobSource, $this->options);
            Log::info("FetchExternalJobsJob completed successfully for '{$jobSource->name}'", $metrics);
        } catch (Exception $e) {
            Log::error("FetchExternalJobsJob failed for '{$jobSource->name}': " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        $sourceName = is_string($this->source) ? $this->source : $this->source->name;
        Log::critical("FetchExternalJobsJob permanently failed for '{$sourceName}': " . ($exception?->getMessage() ?? 'Unknown error'));
    }
}
