<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Job;
use App\Services\Jobs\JobAggregatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobIngestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_ingestion_pipeline_resolves_company_and_creates_jobs(): void
    {
        /** @var JobAggregatorService $aggregator */
        $aggregator = app(JobAggregatorService::class);

        $metrics = $aggregator->ingestFromSource('sample_external_feed');

        $this->assertEquals('sample_external_feed', $metrics['source']);
        $this->assertGreaterThan(0, $metrics['inserted']);

        // Verify external companies created
        $this->assertDatabaseHas('companies', [
            'name' => 'Tata Consultancy Services',
            'is_external' => true,
            'is_claimed' => false,
        ]);

        // Verify job inserted and linked to resolved external company
        $company = Company::where('name', 'Tata Consultancy Services')->first();
        $this->assertNotNull($company);

        $this->assertDatabaseHas('jobs', [
            'company_id' => $company->id,
            'title' => 'Software Developer Intern',
            'job_type' => 'internship',
            'is_remote' => true,
        ]);
    }

    public function test_deduplication_updates_existing_job_on_second_ingestion_run(): void
    {
        /** @var JobAggregatorService $aggregator */
        $aggregator = app(JobAggregatorService::class);

        // Run ingestion first time
        $metrics1 = $aggregator->ingestFromSource('sample_external_feed');
        $initialJobCount = Job::count();

        // Run ingestion second time with identical data
        $metrics2 = $aggregator->ingestFromSource('sample_external_feed');

        $this->assertEquals(0, $metrics2['inserted']);
        $this->assertGreaterThan(0, $metrics2['duplicated']);
        $this->assertEquals($initialJobCount, Job::count());
    }
}
