<?php

namespace App\Console\Commands;

use App\Jobs\FetchExternalJobsJob;
use App\Models\JobSource;
use App\Services\Jobs\JobImportService;
use Exception;
use Illuminate\Console\Command;

class FetchExternalJobsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:fetch
                            {source? : The ID, name, or adapter key of the source to fetch}
                            {--all : Fetch all active external job sources}
                            {--queue : Dispatch as queued jobs instead of running synchronously}
                            {--limit= : Limit the number of records to fetch}
                            {--what= : Search keyword filter (if supported by adapter)}
                            {--where= : Location filter (if supported by adapter)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch opportunities from external job APIs and government feeds into PostgreSQL';

    /**
     * Execute the console command.
     */
    public function handle(JobImportService $importService): int
    {
        $sourceArg = $this->argument('source');
        $fetchAll = $this->option('all');
        $asQueue = $this->option('queue');

        $options = array_filter([
            'limit' => $this->option('limit'),
            'what' => $this->option('what'),
            'where' => $this->option('where'),
        ]);

        if ($fetchAll || empty($sourceArg)) {
            $sources = JobSource::where('is_active', true)
                ->where('type', 'external')
                ->get();

            if ($sources->isEmpty()) {
                $this->warn('No active external job sources found in database.');
                return Command::SUCCESS;
            }

            $this->info("Found {$sources->count()} active external job source(s) to synchronize.");
        } else {
            $sourceQuery = JobSource::query();
            if (\Illuminate\Support\Str::isUuid($sourceArg)) {
                $sourceQuery->where('id', $sourceArg);
            } else {
                $sourceQuery->where(function ($q) use ($sourceArg) {
                    $q->where('adapter_key', $sourceArg)
                      ->orWhere('name', $sourceArg);
                });
            }
            $source = $sourceQuery->first();

            if (! $source) {
                // If it matches a known adapter driver key, create active source on the fly
                $availableDrivers = app(\App\Services\Jobs\JobSourceManager::class)->getAvailableDrivers();
                if (isset($availableDrivers[$sourceArg])) {
                    $source = JobSource::create([
                        'name' => $availableDrivers[$sourceArg]['name'],
                        'type' => 'external',
                        'adapter_key' => $sourceArg,
                        'is_active' => true,
                    ]);
                    $this->info("Created new JobSource record for adapter '{$sourceArg}'.");
                } else {
                    $this->error("Job source '{$sourceArg}' not found.");
                    return Command::FAILURE;
                }
            }

            $sources = collect([$source]);
        }

        $resultsTable = [];

        foreach ($sources as $source) {
            $this->line("<comment>Processing source:</comment> {$source->name} (Adapter: " . ($source->adapter_key ?? 'auto') . ")");

            if ($asQueue) {
                FetchExternalJobsJob::dispatch($source, $options);
                $this->info(" -> Dispatched to queue worker for background processing.");
                $resultsTable[] = [
                    $source->name,
                    'Queued',
                    '—',
                    '—',
                    '—',
                    '—',
                    'Queued',
                ];
                continue;
            }

            try {
                $metrics = $importService->importFromSource($source, $options);

                $resultsTable[] = [
                    $source->name,
                    $metrics['fetched'],
                    $metrics['inserted'],
                    $metrics['updated'],
                    $metrics['rejected'],
                    $metrics['duration_ms'] . ' ms',
                    '<info>Success</info>',
                ];

                $this->info(" -> Synced: {$metrics['inserted']} new, {$metrics['updated']} updated, {$metrics['rejected']} rejected in {$metrics['duration_ms']}ms");
            } catch (Exception $e) {
                $resultsTable[] = [
                    $source->name,
                    '0',
                    '0',
                    '0',
                    '0',
                    '0 ms',
                    '<error>Failed: ' . substr($e->getMessage(), 0, 30) . '</error>',
                ];

                $this->error(" -> Failed: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Source', 'Fetched', 'Inserted', 'Updated', 'Rejected', 'Duration', 'Status'],
            $resultsTable
        );

        return Command::SUCCESS;
    }
}
