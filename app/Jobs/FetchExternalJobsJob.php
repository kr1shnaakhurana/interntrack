<?php

namespace App\Jobs;

use App\Services\Jobs\JobAggregatorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchExternalJobsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $sourceIdentifier = 'sample_external_feed'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(JobAggregatorService $aggregatorService): void
    {
        Log::info("Dispatching FetchExternalJobsJob for source: {$this->sourceIdentifier}");
        $metrics = $aggregatorService->ingestFromSource($this->sourceIdentifier);
        Log::info("FetchExternalJobsJob metrics:", $metrics);
    }
}
