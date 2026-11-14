<?php

namespace App\Services\Jobs\Sources;

use App\Services\Jobs\JobFetchService;
use App\Services\Jobs\JobNormalizationService;
use Exception;
use SimpleXMLElement;

class RssXmlAdapter implements JobSourceAdapterInterface
{
    public function __construct(
        protected JobFetchService $fetchService,
        protected JobNormalizationService $normalizationService,
        protected ?string $feedUrl = null,
        protected ?string $sourceIdentifier = null,
        protected ?string $sourceName = null
    ) {}

    public function getSourceIdentifier(): string
    {
        return $this->sourceIdentifier ?? 'rss_xml';
    }

    public function getSourceName(): string
    {
        return $this->sourceName ?? 'Generic RSS/Atom Feed';
    }

    public function fetchJobs(array $options = []): array
    {
        $feedUrl = $options['feed_url'] ?? $this->feedUrl;
        if (empty($feedUrl)) {
            throw new Exception("Feed URL is required for RSS/XML adapter.");
        }

        $xmlString = $this->fetchService->fetchXml($feedUrl);

        return $this->parseXmlFeed($xmlString);
    }

    /**
     * Safely parse XML string protecting against XXE injection.
     */
    public function parseXmlFeed(string $xmlString): array
    {
        $previousEntityLoader = libxml_disable_entity_loader(true);
        libxml_use_internal_errors(true);

        try {
            // LIBXML_NONET prevents network access during entity expansion
            $xml = simplexml_load_string(trim($xmlString), 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOENT);

            if ($xml === false) {
                $errors = libxml_get_errors();
                libxml_clear_errors();
                $errorMsg = ! empty($errors) ? $errors[0]->message : 'Malformed XML content';
                throw new Exception("Failed to parse RSS/Atom XML: {$errorMsg}");
            }

            $items = [];

            // Case 1: Standard RSS 2.0 (<rss><channel><item>...)
            if (isset($xml->channel->item)) {
                $channelTitle = (string) ($xml->channel->title ?? 'External Feed');
                foreach ($xml->channel->item as $item) {
                    $items[] = $this->extractRssItem($item, $channelTitle);
                }
                return $items;
            }

            // Case 2: Atom feed (<feed><entry>...)
            if (isset($xml->entry)) {
                $feedTitle = (string) ($xml->title ?? 'Atom Feed');
                foreach ($xml->entry as $entry) {
                    $items[] = $this->extractAtomEntry($entry, $feedTitle);
                }
                return $items;
            }

            // Case 3: RDF / RSS 1.0 (<rdf:RDF><item>...)
            if (isset($xml->item)) {
                foreach ($xml->item as $item) {
                    $items[] = $this->extractRssItem($item, 'RSS Feed');
                }
                return $items;
            }

            return [];
        } finally {
            if ($previousEntityLoader !== null && function_exists('libxml_disable_entity_loader')) {
                @libxml_disable_entity_loader($previousEntityLoader);
            }
        }
    }

    protected function extractRssItem(SimpleXMLElement $item, string $channelTitle): array
    {
        $guid = (string) ($item->guid ?? '');
        $link = (string) ($item->link ?? '');
        $title = (string) ($item->title ?? '');
        $description = (string) ($item->description ?? '');

        // Check for Dublin Core / content encoded namespace
        $namespaces = $item->getNamespaces(true);
        if (isset($namespaces['content'])) {
            $contentNs = $item->children($namespaces['content']);
            if (isset($contentNs->encoded) && ! empty((string) $contentNs->encoded)) {
                $description = (string) $contentNs->encoded;
            }
        }

        $author = (string) ($item->author ?? '');
        if (empty($author) && isset($namespaces['dc'])) {
            $dcNs = $item->children($namespaces['dc']);
            $author = (string) ($dcNs->creator ?? '');
        }

        $categories = [];
        if (isset($item->category)) {
            foreach ($item->category as $cat) {
                $categories[] = (string) $cat;
            }
        }

        $pubDate = (string) ($item->pubDate ?? '');

        return [
            'id' => ! empty($guid) ? $guid : (! empty($link) ? $link : md5($title . $pubDate)),
            'title' => $title,
            'link' => $link,
            'description' => $description,
            'pubDate' => $pubDate,
            'channel' => $channelTitle,
            'author' => $author,
            'categories' => $categories,
        ];
    }

    protected function extractAtomEntry(SimpleXMLElement $entry, string $feedTitle): array
    {
        $id = (string) ($entry->id ?? '');
        $title = (string) ($entry->title ?? '');

        // Atom link can be an attribute
        $link = '';
        if (isset($entry->link)) {
            $attrs = $entry->link->attributes();
            $link = (string) ($attrs['href'] ?? (string) $entry->link);
        }

        $content = (string) ($entry->content ?? ($entry->summary ?? ''));
        $updated = (string) ($entry->updated ?? ($entry->published ?? ''));

        $categories = [];
        if (isset($entry->category)) {
            foreach ($entry->category as $cat) {
                $attrs = $cat->attributes();
                $categories[] = (string) ($attrs['term'] ?? (string) $cat);
            }
        }

        return [
            'id' => ! empty($id) ? $id : (! empty($link) ? $link : md5($title . $updated)),
            'title' => $title,
            'link' => $link,
            'description' => $content,
            'pubDate' => $updated,
            'channel' => $feedTitle,
            'author' => (string) ($entry->author->name ?? ''),
            'categories' => $categories,
        ];
    }

    public function normalizeJob(array $rawItem): array
    {
        $title = trim($rawItem['title'] ?? '');
        $description = $this->normalizationService->sanitizeDescription($rawItem['description'] ?? '');
        $companyName = ! empty($rawItem['author']) ? trim($rawItem['author']) : trim($rawItem['channel'] ?? 'External Feed Organization');
        $jobUrl = $this->normalizationService->validateUrl($rawItem['link'] ?? null);
        $postedAt = $this->normalizationService->normalizeDate($rawItem['pubDate'] ?? null);
        $jobType = $this->normalizationService->normalizeJobType(null, $title, $description);

        $categories = $this->normalizationService->normalizeSkills($rawItem['categories'] ?? []);

        return [
            'company_name' => $companyName,
            'company_website' => null,
            'title' => strip_tags($title),
            'description' => $description,
            'job_type' => $jobType,
            'location' => 'Location Details in Notification',
            'is_remote' => false,
            'stipend_salary' => null,
            'deadline' => null,
            'skills_required' => $categories,
            'job_url' => $jobUrl,
            'source_job_id' => (string) ($rawItem['id'] ?? md5($title . $jobUrl)),
            'posted_at' => $postedAt,
            'metadata' => [
                'source_platform' => 'RSS/Atom',
                'channel' => $rawItem['channel'] ?? null,
                'categories' => $rawItem['categories'] ?? [],
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

