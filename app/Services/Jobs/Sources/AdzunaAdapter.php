<?php

namespace App\Services\Jobs\Sources;

use App\Services\Jobs\JobFetchService;
use App\Services\Jobs\JobNormalizationService;
use Exception;
use Illuminate\Support\Facades\Log;

class AdzunaAdapter implements JobSourceAdapterInterface
{
    public function __construct(
        protected JobFetchService $fetchService,
        protected JobNormalizationService $normalizationService,
        protected ?string $appId = null,
        protected ?string $appKey = null,
        protected ?string $country = null
    ) {
        $this->appId = $appId ?? config('services.adzuna.app_id');
        $this->appKey = $appKey ?? config('services.adzuna.app_key');
        $this->country = $country ?? config('services.adzuna.country', 'in');
    }

    public function getSourceIdentifier(): string
    {
        return 'adzuna';
    }

    public function getSourceName(): string
    {
        return 'Adzuna Job Search API';
    }

    public function fetchJobs(array $options = []): array
    {
        $appId = $options['app_id'] ?? $this->appId;
        $appKey = $options['app_key'] ?? $this->appKey;
        $country = strtolower($options['country'] ?? $this->country ?? 'in');
        $page = (int) ($options['page'] ?? 1);
        $resultsPerPage = min((int) ($options['results_per_page'] ?? 20), 50);

        if (empty($appId) || empty($appKey)) {
            Log::warning("Adzuna API credentials (ADZUNA_APP_ID / ADZUNA_APP_KEY) are missing.");
            throw new Exception("Adzuna credentials are not configured. Please set ADZUNA_APP_ID and ADZUNA_APP_KEY in your .env file.");
        }

        $endpoint = "https://api.adzuna.com/v1/api/jobs/{$country}/search/{$page}";

        $queryParams = [
            'app_id' => $appId,
            'app_key' => $appKey,
            'results_per_page' => $resultsPerPage,
            'content-type' => 'application/json',
        ];

        if (! empty($options['what'])) {
            $queryParams['what'] = $options['what'];
        } else {
            $queryParams['what'] = 'internship';
        }

        if (! empty($options['where'])) {
            $queryParams['where'] = $options['where'];
        }

        $data = $this->fetchService->fetchJson($endpoint, $queryParams);

        return $data['results'] ?? [];
    }

    public function normalizeJob(array $rawItem): array
    {
        $title = trim($rawItem['title'] ?? '');
        $description = $this->normalizationService->sanitizeDescription($rawItem['description'] ?? '');
        $companyName = trim($rawItem['company']['display_name'] ?? '');
        $locationName = $rawItem['location']['display_name'] ?? null;
        $isRemote = $this->normalizationService->normalizeRemote(
            false,
            $locationName,
            $title
        );

        $salaryMin = $rawItem['salary_min'] ?? null;
        $salaryMax = $rawItem['salary_max'] ?? null;
        $stipendSalary = $this->normalizationService->normalizeSalary(
            $salaryMin,
            $salaryMax,
            strtoupper($this->country === 'in' ? 'INR' : 'USD')
        );

        $rawJobType = $rawItem['contract_time'] ?? ($rawItem['contract_type'] ?? null);
        $jobType = $this->normalizationService->normalizeJobType($rawJobType, $title, $description);

        $postedAt = $this->normalizationService->normalizeDate($rawItem['created'] ?? null);
        $jobUrl = $this->normalizationService->validateUrl($rawItem['redirect_url'] ?? null);

        $category = $rawItem['category']['label'] ?? ($rawItem['category']['tag'] ?? null);

        return [
            'company_name' => $companyName,
            'company_website' => null,
            'title' => strip_tags($title),
            'description' => $description,
            'job_type' => $jobType,
            'location' => $this->normalizationService->normalizeLocation($locationName, $isRemote),
            'is_remote' => $isRemote,
            'stipend_salary' => $stipendSalary,
            'deadline' => null,
            'skills_required' => $category,
            'job_url' => $jobUrl,
            'source_job_id' => (string) ($rawItem['id'] ?? ''),
            'posted_at' => $postedAt,
            'metadata' => [
                'source_platform' => 'Adzuna',
                'category' => $category,
                'adzuna_id' => $rawItem['id'] ?? null,
                'salary_min' => $salaryMin,
                'salary_max' => $salaryMax,
                'attribution' => 'Job listings provided by Adzuna',
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

