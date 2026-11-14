<?php

namespace App\Services\Jobs\Sources;

use App\Services\Jobs\JobFetchService;
use App\Services\Jobs\JobNormalizationService;

class JobicyAdapter implements JobSourceAdapterInterface
{
    public function __construct(
        protected JobFetchService $fetchService,
        protected JobNormalizationService $normalizationService,
        protected ?string $apiUrl = null
    ) {
        $this->apiUrl = $apiUrl ?? config('services.jobicy.api_url', 'https://jobicy.com/api/v2/remote-jobs');
    }

    public function getSourceIdentifier(): string
    {
        return 'jobicy';
    }

    public function getSourceName(): string
    {
        return 'Jobicy Remote Jobs API';
    }

    public function fetchJobs(array $options = []): array
    {
        $count = min((int) ($options['count'] ?? 20), 50);
        $queryParams = [
            'count' => $count,
        ];

        if (! empty($options['geo'])) {
            $queryParams['geo'] = $options['geo'];
        }

        if (! empty($options['industry'])) {
            $queryParams['industry'] = $options['industry'];
        }

        if (! empty($options['tag'])) {
            $queryParams['tag'] = $options['tag'];
        }

        $data = $this->fetchService->fetchJson($this->apiUrl, $queryParams);

        return $data['jobs'] ?? [];
    }

    public function normalizeJob(array $rawItem): array
    {
        $title = trim($rawItem['jobTitle'] ?? '');
        $description = $this->normalizationService->sanitizeDescription($rawItem['jobDescription'] ?? '');
        $companyName = trim($rawItem['companyName'] ?? '');
        $location = $rawItem['jobGeo'] ?? 'Remote';
        $isRemote = true;

        $rawJobType = is_array($rawItem['jobType'] ?? null)
            ? implode(', ', $rawItem['jobType'])
            : ($rawItem['jobType'] ?? null);

        $jobType = $this->normalizationService->normalizeJobType($rawJobType, $title, $description);

        $salaryMin = $rawItem['salaryMin'] ?? $rawItem['annualSalaryMin'] ?? null;
        $salaryMax = $rawItem['salaryMax'] ?? $rawItem['annualSalaryMax'] ?? null;
        $salaryCurrency = $rawItem['salaryCurrency'] ?? 'USD';
        $salaryPeriod = $rawItem['salaryPeriod'] ?? 'yearly';
        $stipendSalary = $this->normalizationService->normalizeSalary($salaryMin, $salaryMax, $salaryCurrency, null, $salaryPeriod);

        $postedAt = $this->normalizationService->normalizeDate($rawItem['pubDate'] ?? null);
        $jobUrl = $this->normalizationService->validateUrl($rawItem['url'] ?? null);

        $industry = is_array($rawItem['jobIndustry'] ?? null)
            ? implode(', ', $rawItem['jobIndustry'])
            : ($rawItem['jobIndustry'] ?? null);

        return [
            'company_name' => $companyName,
            'company_website' => null,
            'title' => strip_tags($title),
            'description' => $description,
            'job_type' => $jobType,
            'location' => $this->normalizationService->normalizeLocation($location, true),
            'is_remote' => $isRemote,
            'stipend_salary' => $stipendSalary,
            'deadline' => null,
            'skills_required' => $industry,
            'job_url' => $jobUrl,
            'source_job_id' => (string) ($rawItem['id'] ?? ''),
            'posted_at' => $postedAt,
            'metadata' => [
                'source_platform' => 'Jobicy',
                'industry' => $industry,
                'company_logo' => $rawItem['companyLogo'] ?? null,
                'attribution' => 'Job opportunities provided via Jobicy.com',
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

