<?php

namespace App\Services\Jobs\Sources;

use App\Services\Jobs\JobFetchService;
use App\Services\Jobs\JobNormalizationService;
use Exception;
use Illuminate\Support\Arr;

class GenericJsonAdapter implements JobSourceAdapterInterface
{
    public function __construct(
        protected JobFetchService $fetchService,
        protected JobNormalizationService $normalizationService,
        protected ?string $endpoint = null,
        protected array $fieldMapping = [],
        protected ?string $sourceIdentifier = null,
        protected ?string $sourceName = null
    ) {}

    public function getSourceIdentifier(): string
    {
        return $this->sourceIdentifier ?? 'generic_json';
    }

    public function getSourceName(): string
    {
        return $this->sourceName ?? 'Configurable JSON API';
    }

    public function fetchJobs(array $options = []): array
    {
        $endpoint = $options['endpoint'] ?? $this->endpoint;
        if (empty($endpoint)) {
            throw new Exception("API endpoint URL is required for GenericJsonAdapter.");
        }

        $headers = $options['headers'] ?? [];
        $params = $options['params'] ?? [];

        $data = $this->fetchService->fetchJson($endpoint, $params, $headers);

        $rootKey = $options['root_key'] ?? ($this->fieldMapping['root_key'] ?? null);
        if ($rootKey && isset($data[$rootKey]) && is_array($data[$rootKey])) {
            return $data[$rootKey];
        }

        // If top-level array
        if (array_is_list($data)) {
            return $data;
        }

        // Find first array element
        foreach ($data as $val) {
            if (is_array($val) && array_is_list($val)) {
                return $val;
            }
        }

        return [$data];
    }

    public function normalizeJob(array $rawItem): array
    {
        $mapping = $this->fieldMapping;

        $id = (string) Arr::get($rawItem, $mapping['id'] ?? 'id', '');
        $title = (string) Arr::get($rawItem, $mapping['title'] ?? 'title', '');
        $company = (string) Arr::get($rawItem, $mapping['company'] ?? 'company', 'External Company');
        $rawDesc = (string) Arr::get($rawItem, $mapping['description'] ?? 'description', '');
        $description = $this->normalizationService->sanitizeDescription($rawDesc);
        $url = Arr::get($rawItem, $mapping['url'] ?? 'url', null);
        $jobUrl = $this->normalizationService->validateUrl($url);

        $location = Arr::get($rawItem, $mapping['location'] ?? 'location', null);
        $rawRemote = Arr::get($rawItem, $mapping['is_remote'] ?? 'is_remote', false);
        $isRemote = $this->normalizationService->normalizeRemote($rawRemote, $location, $title);

        $salary = Arr::get($rawItem, $mapping['salary'] ?? 'salary', null);
        $rawJobType = Arr::get($rawItem, $mapping['job_type'] ?? 'job_type', null);
        $jobType = $this->normalizationService->normalizeJobType($rawJobType, $title, $description);

        $deadline = Arr::get($rawItem, $mapping['deadline'] ?? 'deadline', null);
        $normalizedDeadline = $this->normalizationService->normalizeDate($deadline);

        $postedAt = Arr::get($rawItem, $mapping['posted_at'] ?? 'posted_at', null);
        $normalizedPostedAt = $this->normalizationService->normalizeDate($postedAt);

        $skills = Arr::get($rawItem, $mapping['skills'] ?? 'skills', null);
        $normalizedSkills = $this->normalizationService->normalizeSkills($skills);

        return [
            'company_name' => trim($company),
            'company_website' => Arr::get($rawItem, $mapping['company_website'] ?? 'company_website', null),
            'title' => strip_tags(trim($title)),
            'description' => $description,
            'job_type' => $jobType,
            'location' => $this->normalizationService->normalizeLocation($location, $isRemote),
            'is_remote' => $isRemote,
            'stipend_salary' => $salary,
            'deadline' => $normalizedDeadline,
            'skills_required' => $normalizedSkills,
            'job_url' => $jobUrl,
            'source_job_id' => $id ?: md5($title . $jobUrl),
            'posted_at' => $normalizedPostedAt,
            'metadata' => [
                'source_platform' => 'Generic JSON API',
                'raw_keys' => array_keys($rawItem),
            ],
        ];
    }

    public function validateJob(array $normalizedItem): bool
    {
        return ! empty($normalizedItem['title'])
            && ! empty($normalizedItem['company_name'])
            && ! empty($normalizedItem['job_url']);
    }
}

