<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifyJobUrlsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:verify-urls
                            {--limit=50 : Maximum number of job URLs to verify per run}
                            {--timeout=5 : Request timeout in seconds}
                            {--dry-run : Report broken URLs without deactivating records}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify external job application links and deactivate dead listings (404/410)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $timeout = (int) $this->option('timeout');
        $dryRun = (bool) $this->option('dry-run');
        $verifySsl = config('services.job_fetch.verify_ssl', true);
        $userAgent = config('services.job_fetch.user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36');

        $this->info("Scanning external job links for broken application URLs (Limit: {$limit})...");
        if ($dryRun) {
            $this->warn(" [DRY RUN MODE ENABLED]");
        }

        $jobs = Job::where('is_active', true)
            ->whereNotNull('job_url')
            ->whereNotNull('source_id')
            ->whereHas('source', fn($q) => $q->where('type', '!=', 'direct'))
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        if ($jobs->isEmpty()) {
            $this->info("No external active jobs with URLs found to verify.");
            return Command::SUCCESS;
        }

        $deactivatedCount = 0;
        $verifiedCount = 0;

        foreach ($jobs as $job) {
            $url = $job->job_url;
            $verifiedCount++;

            try {
                // Try lightweight HEAD request first
                $response = Http::timeout($timeout)
                    ->withOptions(['verify' => $verifySsl])
                    ->withHeaders([
                        'User-Agent' => $userAgent,
                        'Accept' => '*/*',
                    ])
                    ->head($url);

                $status = $response->status();

                // If HEAD returns 405 Method Not Allowed or 403, try quick GET
                if ($status === 405) {
                    $response = Http::timeout($timeout)
                        ->withOptions(['verify' => $verifySsl])
                        ->withHeaders(['User-Agent' => $userAgent])
                        ->get($url);
                    $status = $response->status();
                }

                // Deactivate ONLY on explicit 404 Not Found or 410 Gone
                if ($status === 404 || $status === 410) {
                    $this->error(" [DEAD LINK {$status}] Job '{$job->title}' -> {$url}");
                    $deactivatedCount++;

                    if (! $dryRun) {
                        $job->update([
                            'is_active' => false,
                        ]);
                        Log::info("Deactivated job due to dead URL (HTTP {$status})", [
                            'job_id' => $job->id,
                            'url' => $url,
                        ]);
                    }
                } else {
                    $this->line(" [OK {$status}] Job '{$job->title}'");
                }
            } catch (\Exception $e) {
                // Network failures / timeouts / SSL resets should NOT deactivate jobs automatically
                $this->warn(" [SKIPPED ERROR] Job '{$job->title}' -> " . $e->getMessage());
            }

            // Courteous rate limit delay between probes
            usleep(100000); // 100ms
        }

        $this->info("Finished URL verification. Checked: {$verifiedCount}, Deactivated: {$deactivatedCount}.");

        return Command::SUCCESS;
    }
}

