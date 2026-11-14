<?php

namespace App\Services\Jobs\Sources;

use App\Services\Jobs\JobFetchService;
use App\Services\Jobs\JobNormalizationService;

class RemotiveAdapter implements JobSourceAdapterInterface
{
    public function __construct(
        protected JobFetchService $fetchService,
        protected JobNormalizationService $normalizationService,
        protected ?string $apiUrl = null
    ) {
        $this->apiUrl = $apiUrl ?? config('services.remotive.api_url', 'https://remotive.com/api/remote-jobs');
    }

    public function getSourceIdentifier(): string
    {
        return 'remotive';
    }

    public function getSourceName(): string
    {
        return 'Remotive Remote Jobs API';
    }

    public function fetchJobs(array $options = []): array
    {
        $limit = min((int) ($options['limit'] ?? 25), 100);
        $queryParams = [
            'limit' => $limit,
        ];

        if (! empty($options['search'])) {
            $queryParams['search'] = $options['search'];
        }

        if (! empty($options['category'])) {
            $queryParams['category'] = $options['category'];
        }

        $data = $this->fetchService->fetchJson($this->apiUrl, $queryParams);

        return $data['jobs'] ?? [];
    }

    public function normalizeJob(array $rawItem): array
    {
        $title = trim($rawItem['title'] ?? '');
        $description = $this->normalizationService->sanitizeDescription($rawItem['description'] ?? '');
        $companyName = trim($rawItem['company_name'] ?? '');
        $location = $rawItem['candidate_required_location'] ?? 'Worldwide / Remote';
        $isRemote = true; // Remotive is 100% remote

        $jobType = $this->normalizationService->normalizeJobType(
            $rawItem['job_type'] ?? null,
            $title,
            $description
        );

        $postedAt = $this->normalizationService->normalizeDate($rawItem['publication_date'] ?? null);
        $jobUrl = $this->normalizationService->validateUrl($rawItem['url'] ?? null);
        $skills = $this->normalizationService->normalizeSkills($rawItem['tags'] ?? []);

        return [
            'company_name' => $companyName,
            'company_website' => null,
            'title' => strip_tags($title),
            'description' => $description,
            'job_type' => $jobType,
            'location' => $this->normalizationService->normalizeLocation($location, true),
            'is_remote' => $isRemote,
            'stipend_salary' => ! empty($rawItem['salary']) ? trim($rawItem['salary']) : null,
            'deadline' => null,
            'skills_required' => $skills,
            'job_url' => $jobUrl,
            'source_job_id' => (string) ($rawItem['id'] ?? ''),
            'posted_at' => $postedAt,
            'metadata' => [
                'source_platform' => 'Remotive',
                'category' => $rawItem['category'] ?? null,
                'company_logo' => $rawItem['company_logo'] ?? null,
                'attribution' => 'Job listings sourced from Remotive.com',
            ],
        ];
    }

    public function validateJob(array $normalizedItem): bool
    {
        return ! empty($normalizedItem['title'])
            && ! empty($normalizedItem['company_name'])
            && ! empty($normalizedItem['source_job_id'])
            && ! empty($normalizedItem['job_url'])
            && ! empty(trim(strip_tags($normalizedItem['description'] ?? '')));
    }
}

