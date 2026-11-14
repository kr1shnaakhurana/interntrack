<?php

namespace App\Console\Commands;

use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeactivateStaleJobsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:deactivate-stale
                            {--max-age= : Override max age in days (defaults to config JOB_MAX_AGE_DAYS)}
                            {--dry-run : Report jobs to deactivate without applying changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate expired or stale external jobs without affecting internal direct jobs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $maxAgeDays = (int) ($this->option('max-age') ?? config('services.job_fetch.max_age_days', 90));
        $dryRun = (bool) $this->option('dry-run');
        $now = Carbon::now('UTC');
        $cutoffDate = $now->copy()->subDays($maxAgeDays);

        $this->info("Scanning external jobs for stale or expired listings...");
        $this->line(" - Current time (UTC): {$now->toDateTimeString()}");
        $this->line(" - Freshness cutoff ({$maxAgeDays} days): {$cutoffDate->toDateTimeString()}");
        $this->line(" - Hard cutoff (year <= 2024): 2025-01-01 00:00:00");
        if ($dryRun) {
            $this->warn(" [DRY RUN MODE ENABLED - No database updates will be committed]");
        }

        // ONLY target active external jobs (never direct employer jobs)
        $query = Job::with(['company', 'source'])
            ->where('is_active', true)
            ->whereNotNull('source_id')
            ->whereHas('source', function ($sq) {
                $sq->where('type', '!=', 'direct');
            })
            ->where(function ($q) use ($now, $cutoffDate) {
                // Condition 1: Deadline has passed
                $q->where(function ($dq) use ($now) {
                    $dq->whereNotNull('deadline')
                       ->where('deadline', '<', $now);
                })
                // Condition 2: Published in 2024 or older
                ->orWhere(function ($yq) {
                    $yq->whereNotNull('posted_at')
                       ->where('posted_at', '<', '2025-01-01 00:00:00');
                })
                // Condition 3: Older than max age days when no active deadline is present
                ->orWhere(function ($aq) use ($cutoffDate) {
                    $aq->whereNull('deadline')
                       ->whereNotNull('posted_at')
                       ->where('posted_at', '<', $cutoffDate);
                });
            });

        $staleJobs = $query->get();
        $count = $staleJobs->count();

        if ($count === 0) {
            $this->info("No stale or expired external jobs found. Everything is up to date.");
            return Command::SUCCESS;
        }

        $this->table(
            ['ID', 'Title', 'Company', 'Source', 'Posted At', 'Deadline', 'Reason'],
            $staleJobs->map(function (Job $job) use ($now, $cutoffDate) {
                $reason = 'Stale';
                if ($job->deadline && $job->deadline->lt($now)) {
                    $reason = 'Expired Deadline (' . $job->deadline->toDateString() . ')';
                } elseif ($job->posted_at && $job->posted_at->year <= 2024) {
                    $reason = 'Older than 2025 (' . $job->posted_at->toDateString() . ')';
                } elseif ($job->posted_at && $job->posted_at->lt($cutoffDate)) {
                    $reason = 'Older than ' . config('services.job_fetch.max_age_days', 90) . ' days';
                }

                return [
                    substr($job->id, 0, 8) . '...',
                    $job->title,
                    $job->company?->name ?? 'N/A',
                    $job->source?->name ?? 'External',
                    $job->posted_at?->toDateString() ?? 'N/A',
                    $job->deadline?->toDateString() ?? 'None',
                    $reason,
                ];
            })
        );

        if (! $dryRun) {
            $jobIds = $staleJobs->pluck('id')->toArray();
            Job::whereIn('id', $jobIds)->update(['is_active' => false]);

            Log::info("Deactivated {$count} stale/expired external jobs", ['job_ids' => $jobIds]);
            $this->info("Successfully deactivated {$count} stale/expired external jobs.");
        } else {
            $this->info("Dry run complete: {$count} external jobs would be deactivated.");
        }

        return Command::SUCCESS;
    }
}

