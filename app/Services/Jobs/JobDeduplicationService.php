<?php

namespace App\Services\Jobs;

use App\Models\Job;
use App\Models\JobSource;

class JobDeduplicationService
{
    /**
     * Check if a job already exists via primary key (source_id + source_job_id) or fingerprinting.
     */
    public function findExistingJob(JobSource $source, string $sourceJobId, string $title, string $companyId, ?string $location = null, ?string $deadline = null): ?Job
    {
        // 1. Primary deduplication by source_id + source_job_id
        if (! empty($sourceJobId)) {
            $existing = Job::where('source_id', $source->id)
                ->where('source_job_id', $sourceJobId)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        // 2. Fallback fingerprint match: lower(title) + company_id + lower(location)
        $query = Job::where('company_id', $companyId)
            ->whereRaw('LOWER(title) = ?', [strtolower(trim($title))]);

        if (! empty($location)) {
            $query->whereRaw('LOWER(location) = ?', [strtolower(trim($location))]);
        }

        return $query->first();
    }

    /**
     * Compute SHA-256 fingerprint for analytical logging.
     */
    public function computeFingerprint(string $title, string $companyId, ?string $location, ?string $deadline): string
    {
        $raw = strtolower(trim($title)) . '|' . $companyId . '|' . strtolower(trim($location ?? '')) . '|' . ($deadline ?? '');
        return hash('sha256', $raw);
    }
}
