<?php

namespace App\Services\Jobs;

use App\Models\Job;
use App\Models\JobSource;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobImportService
{
    public function __construct(
        protected JobSourceManager $sourceManager,
        protected CompanyMappingService $companyMappingService,
        protected JobDeduplicationService $deduplicationService
    ) {}

    /**
     * Import opportunities from a specific JobSource model.
     *
     * @param JobSource $jobSource
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function importFromSource(JobSource $jobSource, array $options = []): array
    {
        if (! $jobSource->is_active) {
            throw new Exception("Cannot fetch from disabled job source '{$jobSource->name}'.");
        }

        $startTime = microtime(true);
        $jobSource->update([
            'last_fetch_status' => 'running',
        ]);

        $metrics = [
            'source' => $jobSource->name,
            'source_id' => $jobSource->id,
            'fetched' => 0,
            'normalized' => 0,
            'inserted' => 0,
            'updated' => 0,
            'duplicated' => 0,
            'rejected' => 0,
            'skipped_stale' => 0,
            'skipped_expired' => 0,
            'duration_ms' => 0,
        ];

        $maxAgeDays = (int) config('services.job_fetch.max_age_days', 90);
        $requireDescription = (bool) config('services.job_fetch.require_description', true);
        $requireValidUrl = (bool) config('services.job_fetch.require_valid_url', true);

        try {
            $adapter = $this->sourceManager->resolve($jobSource);
            $rawItems = $adapter->fetchJobs($options);

            $metrics['fetched'] = count($rawItems);

            foreach ($rawItems as $rawItem) {
                try {
                    $normalized = $adapter->normalizeJob($rawItem);

                    if (! $adapter->validateJob($normalized)) {
                        $metrics['rejected']++;
                        continue;
                    }

                    // Quality Gate: Title must be valid and non-empty
                    $title = trim($normalized['title'] ?? '');
                    if (empty($title) || in_array(strtolower($title), ['undefined', 'null', 'test', 'n/a', 'untitled'])) {
                        $metrics['rejected']++;
                        continue;
                    }

                    // Quality Gate: Company name must be valid and non-placeholder
                    $companyName = trim($normalized['company_name'] ?? '');
                    if (empty($companyName) || in_array(strtolower($companyName), ['n/a', 'unknown', 'null', 'none', 'undefined', 'remote employer', 'external organization'])) {
                        $metrics['rejected']++;
                        continue;
                    }

                    // Quality Gate: Meaningful description required
                    $plainDesc = trim(strip_tags($normalized['description'] ?? ''));
                    if ($requireDescription && empty($plainDesc)) {
                        $metrics['rejected']++;
                        continue;
                    }

                    // Quality Gate: Valid HTTP/HTTPS job URL required
                    $jobUrl = $normalized['job_url'] ?? null;
                    if ($requireValidUrl && (empty($jobUrl) || filter_var($jobUrl, FILTER_VALIDATE_URL) === false)) {
                        $metrics['rejected']++;
                        continue;
                    }

                    // Freshness Filter 1: Exclude jobs with deadlines that have already passed
                    if (! empty($normalized['deadline'])) {
                        try {
                            $deadlineCarbon = \Carbon\Carbon::parse($normalized['deadline'])->utc();
                            if ($deadlineCarbon->isPast()) {
                                $metrics['skipped_expired']++;
                                continue;
                            }
                        } catch (\Exception) {
                            // Invalid deadline string format
                        }
                    }

                    // Freshness Filter 2: Exclude jobs older than max_age_days or from 2024 or earlier
                    if (! empty($normalized['posted_at'])) {
                        try {
                            $postedAtCarbon = \Carbon\Carbon::parse($normalized['posted_at'])->utc();
                            if ($postedAtCarbon->year <= 2024) {
                                $metrics['skipped_stale']++;
                                continue;
                            }

                            if ($maxAgeDays > 0 && $postedAtCarbon->lt(now()->utc()->subDays($maxAgeDays))) {
                                $metrics['skipped_stale']++;
                                continue;
                            }
                        } catch (\Exception) {
                            // Invalid date format
                        }
                    }

                    $metrics['normalized']++;

                    // Determine sector hint from metadata
                    $sectorHint = ! empty($normalized['metadata']['is_government']) ? 'government' : null;

                    // Resolve Company
                    $company = $this->companyMappingService->resolveCompany(
                        companyName: $companyName,
                        website: $normalized['company_website'] ?? null,
                        location: $normalized['location'] ?? null,
                        logoUrl: $normalized['metadata']['company_logo'] ?? null,
                        sector: $sectorHint
                    );

                    // Check deduplication
                    $sourceJobId = (string) ($normalized['source_job_id'] ?? '');
                    $existingJob = $this->deduplicationService->findExistingJob(
                        $jobSource,
                        $sourceJobId,
                        $normalized['title'],
                        $company->id,
                        $normalized['location'] ?? null,
                        $normalized['deadline'] ?? null
                    );

                    if ($existingJob) {
                        $metrics['duplicated']++;
                        $metrics['updated']++;

                        // Cautious update: check if expired
                        $isExpired = false;
                        if (! empty($normalized['deadline'])) {
                            $isExpired = \Carbon\Carbon::parse($normalized['deadline'])->isPast();
                        } elseif ($existingJob->deadline && $existingJob->deadline->isPast()) {
                            $isExpired = true;
                        }

                        $updates = ['is_active' => ! $isExpired];

                        if (! empty($normalized['description'])) {
                            $updates['description'] = $normalized['description'];
                        }
                        if (! empty($normalized['stipend_salary'])) {
                            $updates['stipend_salary'] = $normalized['stipend_salary'];
                        }
                        if (! empty($normalized['deadline'])) {
                            $updates['deadline'] = $normalized['deadline'];
                        }
                        if (! empty($normalized['skills_required'])) {
                            $updates['skills_required'] = $normalized['skills_required'];
                        }
                        if (! empty($normalized['job_url'])) {
                            $updates['job_url'] = $normalized['job_url'];
                        }
                        if (! empty($normalized['metadata'])) {
                            $existingMeta = is_array($existingJob->metadata) ? $existingJob->metadata : [];
                            $updates['metadata'] = array_merge($existingMeta, $normalized['metadata']);
                        }

                        $existingJob->update($updates);
                    } else {
                        $metrics['inserted']++;

                        Job::create([
                            'company_id' => $company->id,
                            'source_id' => $jobSource->id,
                            'title' => $normalized['title'],
                            'description' => $normalized['description'],
                            'job_type' => $normalized['job_type'] ?? 'internship',
                            'location' => $normalized['location'] ?? null,
                            'is_remote' => (bool) ($normalized['is_remote'] ?? false),
                            'stipend_salary' => $normalized['stipend_salary'] ?? null,
                            'deadline' => $normalized['deadline'] ?? null,
                            'experience_required' => $normalized['experience_required'] ?? null,
                            'skills_required' => $normalized['skills_required'] ?? null,
                            'job_url' => $normalized['job_url'] ?? null,
                            'source_job_id' => $sourceJobId ?: null,
                            'metadata' => $normalized['metadata'] ?? null,
                            'is_active' => true,
                            'posted_at' => $normalized['posted_at'] ?? now(),
                        ]);
                    }
                } catch (Exception $itemEx) {
                    $metrics['rejected']++;
                    Log::warning("Skipped invalid item from source '{$jobSource->name}': " . $itemEx->getMessage());
                }
            }

            $metrics['duration_ms'] = round((microtime(true) - $startTime) * 1000, 2);

            $currentConfig = is_array($jobSource->config) ? $jobSource->config : [];
            $currentConfig['last_metrics'] = $metrics;

            $jobSource->update([
                'config' => $currentConfig,
                'last_fetched_at' => now(),
                'last_fetch_status' => 'success',
                'last_fetch_error' => null,
            ]);

            Log::info("Job import completed for source '{$jobSource->name}'", $metrics);

            return $metrics;
        } catch (Exception $e) {
            $metrics['duration_ms'] = round((microtime(true) - $startTime) * 1000, 2);

            $jobSource->update([
                'last_fetch_status' => 'failed',
                'last_fetch_error' => $e->getMessage(),
            ]);

            Log::error("Job import failed for source '{$jobSource->name}': " . $e->getMessage());

            throw $e;
        }
    }
}

