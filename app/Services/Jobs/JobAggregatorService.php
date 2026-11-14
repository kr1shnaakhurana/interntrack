<?php

namespace App\Services\Jobs;

use App\Models\JobSource;
use App\Services\Jobs\Sources\JobSourceAdapterInterface;
use App\Services\Jobs\Sources\SampleFeedAdapter;
use Exception;
use Illuminate\Support\Facades\Log;

class JobAggregatorService
{
    public function __construct(
        protected JobSourceManager $sourceManager,
        protected JobImportService $importService
    ) {
        // Register default sample adapter for development/testing
        $this->registerAdapter(new SampleFeedAdapter());
    }

    public function registerAdapter(JobSourceAdapterInterface $adapter): void
    {
        $this->sourceManager->registerAdapter($adapter);
    }

    /**
     * Run full ingestion process for a specific source identifier.
     * Maintained for backward-compatibility with WebAdminController and existing tests.
     */
    public function ingestFromSource(string $sourceIdentifier): array
    {
        // Find existing JobSource or create record
        $sourceRecord = JobSource::firstOrCreate(
            ['name' => $sourceIdentifier],
            [
                'type' => 'external',
                'adapter_key' => $sourceIdentifier,
                'is_active' => true,
            ]
        );

        if (! $sourceRecord->adapter_key) {
            $sourceRecord->update(['adapter_key' => $sourceIdentifier]);
        }

        return $this->importService->importFromSource($sourceRecord);
    }
}
