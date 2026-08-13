<?php

namespace App\Services\Jobs;

use App\Models\Job;
use App\Models\JobSource;
use App\Services\Jobs\Sources\JobSourceAdapterInterface;
use App\Services\Jobs\Sources\SampleFeedAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class JobAggregatorService
{
    protected array $adapters = [];

    public function __construct(
        protected CompanyResolutionService $companyResolutionService,
        protected JobDeduplicationService $deduplicationService
    ) {
        // Register default adapters
        $this->registerAdapter(new SampleFeedAdapter());
    }

    public function registerAdapter(JobSourceAdapterInterface $adapter): void
    {
        $this->adapters[$adapter->getSourceIdentifier()] = $adapter;
    }

    /**
     * Run full ingestion process for a specific source identifier or all registered adapters.
     */
    public function ingestFromSource(string $sourceIdentifier): array
    {
        if (! isset($this->adapters[$sourceIdentifier])) {
            throw new Exception("Job source adapter '{$sourceIdentifier}' is not registered.");
        }

        $adapter = $this->adapters[$sourceIdentifier];

        // Ensure JobSource record exists in database
        $sourceRecord = JobSource::firstOrCreate(
            ['name' => $adapter->getSourceIdentifier()],
            [
                'type' => 'external',
                'is_active' => true,
            ]
        );

        $startTime = microtime(true);
        $rawItems = $adapter->fetchJobs();

        $metrics = [
            'source' => $sourceIdentifier,
            'fetched' => count($rawItems),
            'normalized' => 0,
            'inserted' => 0,
            'updated' => 0,
            'duplicated' => 0,
            'rejected' => 0,
            'duration_ms' => 0,
        ];

        foreach ($rawItems as $rawItem) {
            try {
                $normalized = $adapter->normalizeJob($rawItem);

                if (! $adapter->validateJob($normalized)) {
                    $metrics['rejected']++;
                    continue;
                }

                $metrics['normalized']++;

                // Resolve company entity
                $company = $this->companyResolutionService->resolveCompany(
                    $normalized['company_name'],
                    $normalized['company_website'] ?? null,
                    $normalized['location'] ?? null
                );

                // Check deduplication
                $existingJob = $this->deduplicationService->findExistingJob(
                    $sourceRecord,
                    $normalized['source_job_id'] ?? '',
                    $normalized['title'],
                    $company->id,
                    $normalized['location'] ?? null,
                    $normalized['deadline'] ?? null
                );

                if ($existingJob) {
                    $metrics['duplicated']++;
                    $metrics['updated']++;

                    $existingJob->update([
                        'description' => $normalized['description'],
                        'stipend_salary' => $normalized['stipend_salary'] ?? $existingJob->stipend_salary,
                        'deadline' => $normalized['deadline'] ?? $existingJob->deadline,
                        'skills_required' => $normalized['skills_required'] ?? $existingJob->skills_required,
                        'job_url' => $normalized['job_url'] ?? $existingJob->job_url,
                        'is_active' => true,
                    ]);
                } else {
                    $metrics['inserted']++;

                    Job::create([
                        'company_id' => $company->id,
                        'source_id' => $sourceRecord->id,
                        'title' => $normalized['title'],
                        'description' => $normalized['description'],
                        'job_type' => $normalized['job_type'] ?? 'internship',
                        'location' => $normalized['location'] ?? null,
                        'is_remote' => $normalized['is_remote'] ?? false,
                        'stipend_salary' => $normalized['stipend_salary'] ?? null,
                        'deadline' => $normalized['deadline'] ?? null,
                        'skills_required' => $normalized['skills_required'] ?? null,
                        'job_url' => $normalized['job_url'] ?? null,
                        'source_job_id' => $normalized['source_job_id'] ?? null,
                        'is_active' => true,
                        'posted_at' => now(),
                    ]);
                }
            } catch (Exception $e) {
                $metrics['rejected']++;
                Log::error("Ingestion failed for item in source {$sourceIdentifier}: " . $e->getMessage());
            }
        }

        $metrics['duration_ms'] = round((microtime(true) - $startTime) * 1000, 2);

        Log::info("Ingestion completed for {$sourceIdentifier}", $metrics);

        return $metrics;
    }
}
