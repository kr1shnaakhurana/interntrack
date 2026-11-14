<?php

namespace App\Services\Jobs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class JobFetchService
{
    protected int $defaultTimeout = 20;

    protected string $defaultUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 (compatible; InternTrack/1.0; +https://interntrack.dev)';

    /**
     * Fetch JSON payload from a remote endpoint.
     *
     * @param string $url
     * @param array $queryParams
     * @param array $headers
     * @param int|null $timeout
     * @return array
     * @throws Exception
     */
    public function fetchJson(string $url, array $queryParams = [], array $headers = [], ?int $timeout = null): array
    {
        $timeout = $timeout ?? config('services.job_fetch.timeout', $this->defaultTimeout);
        $verifySsl = config('services.job_fetch.verify_ssl', true);
        $userAgent = config('services.job_fetch.user_agent', $this->defaultUserAgent);

        $response = Http::timeout($timeout)
            ->withOptions(['verify' => $verifySsl])
            ->withHeaders(array_merge([
                'User-Agent' => $userAgent,
                'Accept' => 'application/json, text/plain, */*',
                'Accept-Language' => 'en-US,en;q=0.9',
            ], $headers))
            ->retry(2, 300, function ($exception, $request) {
                // Do not retry 401 or 403 authorization failures
                if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                    return ! in_array($exception->response->status(), [401, 403, 404]);
                }
                return true;
            }, throw: false)
            ->get($url, $queryParams);

        if ($response->status() === 429) {
            Log::warning("Rate limit encountered while fetching from {$url}");
            throw new Exception("API rate limit exceeded (HTTP 429) for {$url}. Please retry after cooling down.");
        }

        if ($response->status() === 401 || $response->status() === 403) {
            Log::error("Authentication failed for {$url} with status {$response->status()}");
            throw new Exception("Authentication failed for {$url} (HTTP {$response->status()}). Check API credentials.");
        }

        if (! $response->successful()) {
            throw new Exception("Failed to fetch from {$url}: HTTP {$response->status()} - {$response->body()}");
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw new Exception("Invalid JSON response received from {$url}");
        }

        return $data;
    }

    /**
     * Fetch raw XML / text payload from a remote endpoint (e.g. RSS/Atom feeds).
     *
     * @param string $url
     * @param array $headers
     * @param int|null $timeout
     * @return string
     * @throws Exception
     */
    public function fetchXml(string $url, array $headers = [], ?int $timeout = null): string
    {
        $timeout = $timeout ?? config('services.job_fetch.timeout', $this->defaultTimeout);
        $verifySsl = config('services.job_fetch.verify_ssl', true);
        $userAgent = config('services.job_fetch.user_agent', $this->defaultUserAgent);

        $response = Http::timeout($timeout)
            ->withOptions(['verify' => $verifySsl])
            ->withHeaders(array_merge([
                'User-Agent' => $userAgent,
                'Accept' => 'application/rss+xml, application/atom+xml, application/xml, text/xml, */*',
                'Accept-Language' => 'en-US,en;q=0.9',
            ], $headers))
            ->retry(2, 300, throw: false)
            ->get($url);

        if ($response->status() === 429) {
            throw new Exception("Feed rate limit exceeded (HTTP 429) for {$url}");
        }

        if (! $response->successful()) {
            throw new Exception("Failed to fetch XML feed from {$url}: HTTP {$response->status()}");
        }

        $body = $response->body();
        if (empty(trim($body))) {
            throw new Exception("Empty feed response received from {$url}");
        }

        return $body;
    }
}
