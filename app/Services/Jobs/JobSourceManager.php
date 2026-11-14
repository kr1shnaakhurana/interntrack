<?php

namespace App\Services\Jobs;

use App\Models\JobSource;
use App\Services\Jobs\Sources\AdzunaAdapter;
use App\Services\Jobs\Sources\GenericJsonAdapter;
use App\Services\Jobs\Sources\GovernmentFeedAdapter;
use App\Services\Jobs\Sources\JobicyAdapter;
use App\Services\Jobs\Sources\JobSourceAdapterInterface;
use App\Services\Jobs\Sources\RemotiveAdapter;
use App\Services\Jobs\Sources\RssXmlAdapter;
use App\Services\Jobs\Sources\SampleFeedAdapter;
use Exception;
use Illuminate\Contracts\Container\Container;

class JobSourceManager
{
    protected array $customAdapters = [];

    public function __construct(
        protected Container $container,
        protected JobFetchService $fetchService,
        protected JobNormalizationService $normalizationService
    ) {}

    /**
     * Register a custom runtime adapter instance.
     */
    public function registerAdapter(JobSourceAdapterInterface $adapter): void
    {
        $this->customAdapters[$adapter->getSourceIdentifier()] = $adapter;
    }

    /**
     * Resolve an adapter instance for a JobSource model or identifier.
     *
     * @param JobSource|string $source
     * @return JobSourceAdapterInterface
     * @throws Exception
     */
    public function resolve(JobSource|string $source): JobSourceAdapterInterface
    {
        if (is_string($source)) {
            if (isset($this->customAdapters[$source])) {
                return $this->customAdapters[$source];
            }

            // Look up by adapter_key or name (or UUID) in DB
            $dbSourceQuery = JobSource::query();
            if (\Illuminate\Support\Str::isUuid($source)) {
                $dbSourceQuery->where('id', $source);
            } else {
                $dbSourceQuery->where(function ($q) use ($source) {
                    $q->where('adapter_key', $source)
                      ->orWhere('name', $source);
                });
            }
            $dbSource = $dbSourceQuery->first();

            if ($dbSource) {
                return $this->createAdapterForModel($dbSource);
            }

            return $this->createAdapterByKey($source);
        }

        return $this->createAdapterForModel($source);
    }

    /**
     * Build adapter instance from a JobSource database model.
     */
    public function createAdapterForModel(JobSource $jobSource): JobSourceAdapterInterface
    {
        $key = $jobSource->adapter_key ?: $this->guessAdapterKey($jobSource);

        if (isset($this->customAdapters[$key])) {
            return $this->customAdapters[$key];
        }

        $config = $jobSource->config ?? [];
        $fieldMapping = $jobSource->field_mapping ?? [];
        $url = $jobSource->api_endpoint ?: $jobSource->base_url;

        return match ($key) {
            'adzuna' => new AdzunaAdapter(
                $this->fetchService,
                $this->normalizationService,
                $config['app_id'] ?? null,
                $config['app_key'] ?? null,
                $config['country'] ?? null
            ),
            'remotive' => new RemotiveAdapter(
                $this->fetchService,
                $this->normalizationService,
                $url ?: null
            ),
            'jobicy' => new JobicyAdapter(
                $this->fetchService,
                $this->normalizationService,
                $url ?: null
            ),
            'government_rss' => new GovernmentFeedAdapter(
                $this->fetchService,
                $this->normalizationService,
                $url ?: null,
                'government_rss',
                $jobSource->name
            ),
            'rss_xml' => new RssXmlAdapter(
                $this->fetchService,
                $this->normalizationService,
                $url ?: null,
                'rss_xml',
                $jobSource->name
            ),
            'generic_json' => new GenericJsonAdapter(
                $this->fetchService,
                $this->normalizationService,
                $url ?: null,
                $fieldMapping,
                'generic_json',
                $jobSource->name
            ),
            'sample_external_feed' => new SampleFeedAdapter(),
            default => throw new Exception("Unsupported job source adapter key: '{$key}'"),
        };
    }

    /**
     * Instantiate adapter strictly by driver key with system default config.
     */
    public function createAdapterByKey(string $key): JobSourceAdapterInterface
    {
        return match ($key) {
            'adzuna' => new AdzunaAdapter($this->fetchService, $this->normalizationService),
            'remotive' => new RemotiveAdapter($this->fetchService, $this->normalizationService),
            'jobicy' => new JobicyAdapter($this->fetchService, $this->normalizationService),
            'government_rss' => new GovernmentFeedAdapter($this->fetchService, $this->normalizationService),
            'rss_xml' => new RssXmlAdapter($this->fetchService, $this->normalizationService),
            'sample_external_feed' => new SampleFeedAdapter(),
            default => throw new Exception("No adapter available for key '{$key}'"),
        };
    }

    /**
     * Guess adapter key from name or attributes if not set.
     */
    protected function guessAdapterKey(JobSource $jobSource): string
    {
        $name = strtolower($jobSource->name);

        if (str_contains($name, 'adzuna')) {
            return 'adzuna';
        }
        if (str_contains($name, 'remotive')) {
            return 'remotive';
        }
        if (str_contains($name, 'jobicy')) {
            return 'jobicy';
        }
        if (str_contains($name, 'govt') || str_contains($name, 'government') || str_contains($name, 'upsc') || str_contains($name, 'ssc')) {
            return 'government_rss';
        }
        if (str_contains($name, 'rss') || str_contains($name, 'atom') || str_contains($name, 'feed')) {
            return 'rss_xml';
        }
        if (str_contains($name, 'sample')) {
            return 'sample_external_feed';
        }

        return 'generic_json';
    }

    /**
     * Get list of supported adapter options for admin dropdown.
     */
    public function getAvailableDrivers(): array
    {
        return [
            'adzuna' => [
                'name' => 'Adzuna Job Search API',
                'description' => 'Global job search engine with keyword, location & salary support via API keys',
                'auth_type' => 'api_key',
                'requires_endpoint' => false,
            ],
            'remotive' => [
                'name' => 'Remotive Remote Jobs API',
                'description' => 'Curated remote developer and tech opportunities feed (No API key required)',
                'auth_type' => 'none',
                'requires_endpoint' => false,
            ],
            'jobicy' => [
                'name' => 'Jobicy Remote Jobs API',
                'description' => 'Remote jobs & internships with category and geographic filters',
                'auth_type' => 'none',
                'requires_endpoint' => false,
            ],
            'government_rss' => [
                'name' => 'Official Government Recruitment RSS/Atom Feed',
                'description' => 'Parses official notifications (UPSC, SSC, Railway, Defence, Banking, State PSCs)',
                'auth_type' => 'none',
                'requires_endpoint' => true,
            ],
            'rss_xml' => [
                'name' => 'Generic RSS / XML Feed',
                'description' => 'Parses standard RSS 2.0 or Atom feeds safely',
                'auth_type' => 'none',
                'requires_endpoint' => true,
            ],
            'generic_json' => [
                'name' => 'Custom JSON API (Field Mapped)',
                'description' => 'Configurable REST API endpoint with custom JSON field mappings',
                'auth_type' => 'api_key',
                'requires_endpoint' => true,
            ],
            'sample_external_feed' => [
                'name' => 'Sample Test Feed (Mock/Offline)',
                'description' => 'Pre-populated offline dataset for automated testing & development verification',
                'auth_type' => 'none',
                'requires_endpoint' => false,
            ],
        ];
    }
}
