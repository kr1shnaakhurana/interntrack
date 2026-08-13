<?php

namespace App\Services\Jobs\Sources;

interface JobSourceAdapterInterface
{
    /**
     * Get the unique string identifier for this source adapter.
     */
    public function getSourceIdentifier(): string;

    /**
     * Fetch raw job items from the external source feed/API.
     */
    public function fetchJobs(): array;

    /**
     * Normalize a single raw external item into a standard associative array DTO.
     */
    public function normalizeJob(array $rawItem): array;

    /**
     * Validate whether the normalized item meets structural quality rules.
     */
    public function validateJob(array $normalizedItem): bool;
}
