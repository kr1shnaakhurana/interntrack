<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobSource;
use App\Models\User;
use App\Services\Jobs\JobImportService;
use App\Services\Jobs\JobSourceManager;
use App\Services\Jobs\Sources\AdzunaAdapter;
use App\Services\Jobs\Sources\GovernmentFeedAdapter;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExternalJobIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin user
        $this->adminUser = User::create([
            'name' => 'Admin Officer',
            'email' => 'admin_test@interntrack.dev',
            'password' => 'secret123',
            'role' => 'admin',
            'is_active' => true,
        ]);
        Admin::create([
            'user_id' => $this->adminUser->id,
            'name' => 'Admin Officer',
        ]);

        // Create Student user
        $this->studentUser = User::create([
            'name' => 'Rohan Sharma',
            'email' => 'rohan_test@interntrack.dev',
            'password' => 'secret123',
            'role' => 'student',
            'is_active' => true,
        ]);
        \App\Models\Student::create([
            'user_id' => $this->studentUser->id,
            'roll_no' => 'STU-9921',
            'program' => 'B.Tech Computer Science',
        ]);
    }

    /**
     * 1. Successful API response
     */
    public function test_1_successful_api_response(): void
    {
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response([
                'jobs' => [
                    [
                        'id' => 901,
                        'url' => 'https://remotive.com/jobs/901',
                        'title' => 'Backend Engineering Intern',
                        'company_name' => 'Stripe Platform',
                        'category' => 'software-dev',
                        'job_type' => 'internship',
                        'publication_date' => '2026-10-15T10:00:00Z',
                        'candidate_required_location' => 'Worldwide',
                        'salary' => '$4,000 / month',
                        'description' => '<p>Develop robust REST APIs using PHP and PostgreSQL.</p>',
                        'tags' => ['PHP', 'Laravel', 'PostgreSQL'],
                    ]
                ]
            ], 200),
        ]);

        $source = JobSource::create([
            'name' => 'Remotive Remote Jobs',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        /** @var JobImportService $importService */
        $importService = app(JobImportService::class);
        $metrics = $importService->importFromSource($source);

        $this->assertEquals(1, $metrics['fetched']);
        $this->assertEquals(1, $metrics['inserted']);
        $this->assertEquals(0, $metrics['rejected']);

        $this->assertDatabaseHas('jobs', [
            'source_id' => $source->id,
            'source_job_id' => '901',
            'title' => 'Backend Engineering Intern',
            'job_type' => 'internship',
            'is_remote' => true,
            'stipend_salary' => '$4,000 / month',
            'job_url' => 'https://remotive.com/jobs/901',
        ]);

        $source->refresh();
        $this->assertEquals('success', $source->last_fetch_status);
        $this->assertNotNull($source->last_fetched_at);
        $this->assertNull($source->last_fetch_error);
    }

    /**
     * 2. Failed API request (HTTP 500)
     */
    public function test_2_failed_api_request(): void
    {
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response('Internal Server Error', 500),
        ]);

        $source = JobSource::create([
            'name' => 'Failing Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);

        $this->expectException(Exception::class);
        try {
            $importService->importFromSource($source);
        } finally {
            $source->refresh();
            $this->assertEquals('failed', $source->last_fetch_status);
            $this->assertNotNull($source->last_fetch_error);
        }
    }

    /**
     * 3. Invalid API response (malformed / non-JSON)
     */
    public function test_3_invalid_api_response(): void
    {
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response('<html>Gateway Timeout</html>', 200, ['Content-Type' => 'text/html']),
        ]);

        $source = JobSource::create([
            'name' => 'Corrupt Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);

        $this->expectException(Exception::class);
        try {
            $importService->importFromSource($source);
        } finally {
            $source->refresh();
            $this->assertEquals('failed', $source->last_fetch_status);
            $this->assertStringContainsString('Invalid JSON', $source->last_fetch_error);
        }
    }

    /**
     * 4. Missing optional fields
     */
    public function test_4_missing_optional_fields(): void
    {
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response([
                'jobs' => [
                    [
                        'id' => 'MIN-881',
                        'url' => 'https://remotive.com/jobs/MIN-881',
                        'title' => 'Frontend Junior Developer',
                        'company_name' => 'Minimal Co',
                        'description' => 'Frontend engineering responsibilities and specifications.',
                        // Omit category, job_type, salary, location, tags, deadline
                    ]
                ]
            ], 200),
        ]);

        $source = JobSource::create([
            'name' => 'Remotive Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);
        $metrics = $importService->importFromSource($source);

        $this->assertEquals(1, $metrics['inserted']);
        $this->assertDatabaseHas('jobs', [
            'source_job_id' => 'MIN-881',
            'title' => 'Frontend Junior Developer',
            'stipend_salary' => null,
            'skills_required' => null,
        ]);
    }

    /**
     * 5. Duplicate external job
     */
    public function test_5_duplicate_external_job(): void
    {
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response([
                'jobs' => [
                    [
                        'id' => 'DUP-001',
                        'url' => 'https://remotive.com/jobs/DUP-001',
                        'title' => 'Cloud Systems Fellow',
                        'company_name' => 'Red Hat Inc',
                        'description' => 'Linux kernel & cloud systems development.',
                    ]
                ]
            ], 200),
        ]);

        $source = JobSource::create([
            'name' => 'Remotive Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);

        // Run 1: First insertion
        $metrics1 = $importService->importFromSource($source);
        $this->assertEquals(1, $metrics1['inserted']);
        $initialJobCount = Job::count();

        // Run 2: Exact duplicate
        $metrics2 = $importService->importFromSource($source);
        $this->assertEquals(0, $metrics2['inserted']);
        $this->assertEquals(1, $metrics2['duplicated']);
        $this->assertEquals($initialJobCount, Job::count());
    }

    /**
     * 6. Existing external job update
     */
    public function test_6_existing_external_job_update(): void
    {
        $source = JobSource::create([
            'name' => 'Remotive Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);

        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::sequence()
                ->push([
                    'jobs' => [
                        [
                            'id' => 'UPD-007',
                            'url' => 'https://remotive.com/jobs/UPD-007',
                            'title' => 'AI Research Intern',
                            'company_name' => 'Open Innovations',
                            'salary' => '$2,500 / month',
                            'description' => 'Original description.',
                        ]
                    ]
                ], 200)
                ->push([
                    'jobs' => [
                        [
                            'id' => 'UPD-007',
                            'url' => 'https://remotive.com/jobs/UPD-007',
                            'title' => 'AI Research Intern',
                            'company_name' => 'Open Innovations',
                            'salary' => '$3,500 / month',
                            'description' => 'Updated description with higher stipend.',
                        ]
                    ]
                ], 200),
        ]);

        // First run with initial stipend
        $importService->importFromSource($source);
        $job = Job::where('source_job_id', 'UPD-007')->first();
        $this->assertNotNull($job);
        $originalId = $job->id;
        $this->assertEquals('$2,500 / month', $job->stipend_salary);

        // Second run with updated stipend
        $importService->importFromSource($source);
        $job->refresh();

        $this->assertEquals($originalId, $job->id); // UUID preserved
        $this->assertEquals('$3,500 / month', $job->stipend_salary); // Updated field
        $this->assertEquals('Updated description with higher stipend.', $job->description);
    }

    /**
     * 7. Company mapping
     */
    public function test_7_company_mapping(): void
    {
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response([
                'jobs' => [
                    [
                        'id' => 'CMP-1',
                        'url' => 'https://remotive.com/jobs/1',
                        'title' => 'Software Engineer 1',
                        'company_name' => 'Anthropic AI',
                        'description' => 'Role 1',
                    ],
                    [
                        'id' => 'CMP-2',
                        'url' => 'https://remotive.com/jobs/2',
                        'title' => 'Software Engineer 2',
                        'company_name' => 'Anthropic AI',
                        'description' => 'Role 2',
                    ]
                ]
            ], 200),
        ]);

        $source = JobSource::create([
            'name' => 'Remotive Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);
        $importService->importFromSource($source);

        $companies = Company::where('name', 'Anthropic AI')->get();
        $this->assertCount(1, $companies);

        $company = $companies->first();
        $this->assertTrue($company->is_external);
        $this->assertFalse($company->is_claimed);
        $this->assertNull($company->user_id); // No user account created

        $jobs = Job::where('company_id', $company->id)->get();
        $this->assertCount(2, $jobs);
    }

    /**
     * 8. Multiple pages of API results
     */
    public function test_8_multiple_pages_of_api_results(): void
    {
        Http::fake([
            'https://api.adzuna.com/v1/api/jobs/in/search/1*' => Http::response([
                'results' => [
                    [
                        'id' => 'ADZ-P1-01',
                        'title' => 'Page 1 Intern',
                        'description' => 'Job on page 1',
                        'company' => ['display_name' => 'Adzuna Partner 1'],
                        'location' => ['display_name' => 'Bengaluru, India'],
                        'redirect_url' => 'https://adzuna.in/jobs/p1-01',
                        'contract_time' => 'full_time',
                    ]
                ]
            ], 200),
            'https://api.adzuna.com/v1/api/jobs/in/search/2*' => Http::response([
                'results' => [
                    [
                        'id' => 'ADZ-P2-01',
                        'title' => 'Page 2 Intern',
                        'description' => 'Job on page 2',
                        'company' => ['display_name' => 'Adzuna Partner 2'],
                        'location' => ['display_name' => 'Pune, India'],
                        'redirect_url' => 'https://adzuna.in/jobs/p2-01',
                        'contract_time' => 'full_time',
                    ]
                ]
            ], 200),
        ]);

        $adapter = new AdzunaAdapter(
            app(\App\Services\Jobs\JobFetchService::class),
            app(\App\Services\Jobs\JobNormalizationService::class),
            'test_app_id',
            'test_app_key',
            'in'
        );

        $page1Results = $adapter->fetchJobs(['page' => 1]);
        $page2Results = $adapter->fetchJobs(['page' => 2]);

        $this->assertCount(1, $page1Results);
        $this->assertCount(1, $page2Results);
        $this->assertEquals('ADZ-P1-01', $page1Results[0]['id']);
        $this->assertEquals('ADZ-P2-01', $page2Results[0]['id']);
    }

    /**
     * 9. API rate-limit response (HTTP 429)
     */
    public function test_9_api_rate_limit_response(): void
    {
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response('Too Many Requests', 429),
        ]);

        $source = JobSource::create([
            'name' => 'Rate Limited Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);

        try {
            $importService->importFromSource($source);
            $this->fail('Expected rate limit exception was not thrown');
        } catch (Exception $e) {
            $this->assertStringContainsString('rate limit', strtolower($e->getMessage()));
            $source->refresh();
            $this->assertEquals('failed', $source->last_fetch_status);
            $this->assertStringContainsString('rate limit', strtolower($source->last_fetch_error));
        }
    }

    /**
     * 10. Disabled job source
     */
    public function test_10_disabled_job_source(): void
    {
        $source = JobSource::create([
            'name' => 'Inactive Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'is_active' => false,
        ]);

        $importService = app(JobImportService::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Cannot fetch from disabled job source 'Inactive Source'");
        $importService->importFromSource($source);
    }

    /**
     * 11. Unauthorized manual sync
     */
    public function test_11_unauthorized_manual_sync(): void
    {
        $source = JobSource::create([
            'name' => 'Protected Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'is_active' => true,
        ]);

        // Case A: Unauthenticated guest attempt
        $responseGuest = $this->post("/admin/job-sources/{$source->id}/sync");
        $responseGuest->assertRedirect('/login');

        // Case B: Authenticated Student attempt
        $responseStudent = $this->actingAs($this->studentUser)->post("/admin/job-sources/{$source->id}/sync");
        $responseStudent->assertForbidden();

        // Case C: Authenticated Admin attempt
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response(['jobs' => []], 200),
        ]);
        $responseAdmin = $this->actingAs($this->adminUser)->post("/admin/job-sources/{$source->id}/sync");
        $responseAdmin->assertSessionHas('success');
    }

    /**
     * 12. Job visibility in student dashboard
     */
    public function test_12_job_visibility_in_student_dashboard(): void
    {
        $company = Company::create([
            'name' => 'Google Cloud India',
            'is_external' => true,
            'is_claimed' => false,
            'sector' => 'private',
        ]);

        $source = JobSource::create([
            'name' => 'Adzuna API',
            'type' => 'external',
            'adapter_key' => 'adzuna',
            'is_active' => true,
        ]);

        $job = Job::create([
            'company_id' => $company->id,
            'source_id' => $source->id,
            'title' => 'Cloud Architecture Associate Intern',
            'description' => 'Work on Google Cloud platform deployments.',
            'job_type' => 'internship',
            'location' => 'Hyderabad, India',
            'is_remote' => false,
            'job_url' => 'https://careers.google.com/jobs/results/12345',
            'source_job_id' => 'GCL-12345',
            'is_active' => true,
            'posted_at' => now(),
        ]);

        $response = $this->actingAs($this->studentUser)->get('/jobs?keyword=Architecture');
        $response->assertOk();
        $response->assertSee('Cloud Architecture Associate Intern');
        $response->assertSee('Google Cloud India');
        $response->assertSee('Adzuna API');
    }

    /**
     * 13. Correct external application URL & redirect
     */
    public function test_13_correct_external_application_url(): void
    {
        $company = Company::create([
            'name' => 'Microsoft Research',
            'is_external' => true,
            'is_claimed' => false,
            'sector' => 'private',
        ]);

        $source = JobSource::create([
            'name' => 'External Portal',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'is_active' => true,
        ]);

        $externalUrl = 'https://careers.microsoft.com/us/en/job/109823/Research-Fellow';

        $job = Job::create([
            'company_id' => $company->id,
            'source_id' => $source->id,
            'title' => 'Quantum Computing Research Fellow',
            'description' => 'Fellowship opportunity in quantum information science.',
            'job_type' => 'research',
            'job_url' => $externalUrl,
            'source_job_id' => 'MS-QF-109823',
            'is_active' => true,
        ]);

        // Verify show page contains the external application URL button
        $responseShow = $this->actingAs($this->studentUser)->get("/jobs/{$job->id}");
        $responseShow->assertOk();
        $responseShow->assertSee($externalUrl);
        $responseShow->assertSee('Apply on Official Portal');

        // Verify attempting to submit application redirects to external URL and does NOT create internal application record
        $responseApply = $this->actingAs($this->studentUser)->post("/jobs/{$job->id}/apply");
        $responseApply->assertRedirect($externalUrl);

        $this->assertDatabaseMissing('applications', [
            'student_id' => $this->studentUser->student->id,
            'job_id' => $job->id,
        ]);
    }

    /**
     * Test Government Recruitment RSS Feed parsing & structured metadata extraction
     */
    public function test_government_recruitment_rss_feed_parsing(): void
    {
        $mockXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Union Public Service Commission (UPSC)</title>
        <link>https://www.upsc.gov.in</link>
        <description>Official Recruitment Notices</description>
        <item>
            <title>UPSC Recruitment 2026: Combined Defence Services (CDS) - 450 Vacancies, Apply Online</title>
            <link>https://upsc.gov.in/recruitment/cds-2026-notification.pdf</link>
            <guid>UPSC-CDS-2026-01</guid>
            <pubDate>Mon, 18 Oct 2026 09:00:00 GMT</pubDate>
            <description><![CDATA[
                Union Public Service Commission invites applications for 450 posts of Officers.
                Eligibility: Graduate Degree / B.E / B.Tech.
                Age Limit: 19 to 24 years.
                Pay Scale: Level 10 Pay Matrix.
                Last Date: 30-12-2026.
            ]]></description>
            <category>Defence</category>
        </item>
    </channel>
</rss>
XML;

        Http::fake([
            'https://www.upsc.gov.in/rss.xml' => Http::response($mockXml, 200, ['Content-Type' => 'application/xml']),
        ]);

        $source = JobSource::create([
            'name' => 'UPSC Official Recruitment Feed',
            'type' => 'external',
            'adapter_key' => 'government_rss',
            'base_url' => 'https://www.upsc.gov.in',
            'api_endpoint' => 'https://www.upsc.gov.in/rss.xml',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);
        $metrics = $importService->importFromSource($source);

        $this->assertEquals(1, $metrics['inserted']);

        $job = Job::where('source_job_id', 'UPSC-CDS-2026-01')->first();
        $this->assertNotNull($job);

        // Verify company sector is government
        $this->assertEquals('government', $job->company->sector);
        $this->assertTrue($job->company->is_external);

        // Verify government classification and extracted metadata
        $this->assertNotNull($job->metadata);
        $this->assertTrue($job->metadata['is_government']);
        $this->assertEquals('Defence', $job->metadata['government_category']);
        $this->assertEquals('450 Posts', $job->metadata['vacancies_count']);
        $this->assertEquals('19 to 24 years', $job->metadata['age_limit']);
        $this->assertStringContainsString('Bachelor\'s Degree', $job->metadata['eligibility_criteria']);
        $this->assertEquals('Level 10 Pay Matrix', $job->stipend_salary);
        $this->assertEquals('https://upsc.gov.in/recruitment/cds-2026-notification.pdf', $job->job_url);
    }

    /**
     * 15. Stale jobs (> 90 days or <= 2024) are rejected during import
     */
    public function test_15_stale_jobs_older_than_90_days_are_rejected(): void
    {
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response([
                'jobs' => [
                    [
                        'id' => 'OLD-2024',
                        'url' => 'https://remotive.com/jobs/OLD-2024',
                        'title' => 'Ancient Legacy Engineer',
                        'company_name' => 'Legacy Corp',
                        'description' => 'Working on 2024 legacy codebase.',
                        'publication_date' => '2024-05-10T10:00:00Z', // 2024 job!
                    ],
                    [
                        'id' => 'OLD-120-DAYS',
                        'url' => 'https://remotive.com/jobs/OLD-120-DAYS',
                        'title' => 'Expired Opportunity',
                        'company_name' => 'Stale Ventures',
                        'description' => 'Published 120 days ago.',
                        'publication_date' => now()->subDays(120)->toIso8601String(), // > 90 days!
                    ],
                    [
                        'id' => 'FRESH-JOB-2026',
                        'url' => 'https://remotive.com/jobs/FRESH-JOB-2026',
                        'title' => 'Fresh 2026 Opportunity',
                        'company_name' => 'Modern Labs',
                        'description' => 'Published yesterday.',
                        'publication_date' => now()->subDays(2)->toIso8601String(),
                    ],
                ]
            ], 200),
        ]);

        $source = JobSource::create([
            'name' => 'Remotive Freshness Test',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);
        $metrics = $importService->importFromSource($source);

        $this->assertEquals(3, $metrics['fetched']);
        $this->assertEquals(2, $metrics['skipped_stale']);
        $this->assertEquals(1, $metrics['inserted']);

        $this->assertDatabaseMissing('jobs', ['source_job_id' => 'OLD-2024']);
        $this->assertDatabaseMissing('jobs', ['source_job_id' => 'OLD-120-DAYS']);
        $this->assertDatabaseHas('jobs', ['source_job_id' => 'FRESH-JOB-2026']);
    }

    /**
     * 16. Expired deadline jobs are rejected during import
     */
    public function test_16_expired_deadline_jobs_rejected(): void
    {
        $mockXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
    <channel>
        <title>Recruitment Board</title>
        <link>https://govt.gov.in</link>
        <item>
            <title>Officer Recruitment - Past Deadline</title>
            <link>https://govt.gov.in/past</link>
            <guid>GOVT-PAST-01</guid>
            <pubDate>Mon, 01 Nov 2026 09:00:00 GMT</pubDate>
            <description><![CDATA[
                Recruitment notice. Last Date: 05-11-2026.
            ]]></description>
        </item>
    </channel>
</rss>
XML;

        Http::fake([
            'https://govt.gov.in/rss.xml' => Http::response($mockXml, 200),
        ]);

        $source = JobSource::create([
            'name' => 'Expired Govt Feed',
            'type' => 'external',
            'adapter_key' => 'government_rss',
            'api_endpoint' => 'https://govt.gov.in/rss.xml',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);
        $metrics = $importService->importFromSource($source);

        $this->assertEquals(1, $metrics['fetched']);
        $this->assertEquals(1, $metrics['skipped_expired']);
        $this->assertEquals(0, $metrics['inserted']);
        $this->assertDatabaseMissing('jobs', ['source_job_id' => 'GOVT-PAST-01']);
    }

    /**
     * 17. Blank / invalid jobs rejected by quality gate (no placeholder cards)
     */
    public function test_17_blank_and_invalid_jobs_rejected_by_quality_gate(): void
    {
        Http::fake([
            'https://remotive.com/api/remote-jobs*' => Http::response([
                'jobs' => [
                    [
                        // Blank title
                        'id' => 'BLANK-1',
                        'url' => 'https://remotive.com/jobs/1',
                        'title' => '   ',
                        'company_name' => 'Real Co',
                        'description' => 'Real description.',
                    ],
                    [
                        // Blank company
                        'id' => 'BLANK-2',
                        'url' => 'https://remotive.com/jobs/2',
                        'title' => 'Valid Title',
                        'company_name' => '',
                        'description' => 'Real description.',
                    ],
                    [
                        // Empty description
                        'id' => 'BLANK-3',
                        'url' => 'https://remotive.com/jobs/3',
                        'title' => 'Valid Title',
                        'company_name' => 'Real Co',
                        'description' => '   ',
                    ],
                    [
                        // Invalid URL
                        'id' => 'BLANK-4',
                        'url' => 'not-a-valid-url',
                        'title' => 'Valid Title',
                        'company_name' => 'Real Co',
                        'description' => 'Real description.',
                    ],
                ]
            ], 200),
        ]);

        $source = JobSource::create([
            'name' => 'Invalid Test Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'api_endpoint' => 'https://remotive.com/api/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);
        $metrics = $importService->importFromSource($source);

        $this->assertEquals(4, $metrics['fetched']);
        $this->assertEquals(4, $metrics['rejected']);
        $this->assertEquals(0, $metrics['inserted']);
        $this->assertEquals(0, Job::count());
    }

    /**
     * 18. Deactivate stale command deactivates external expired jobs without touching internal direct jobs
     */
    public function test_18_deactivate_stale_command_preserves_internal_direct_jobs(): void
    {
        $directSource = JobSource::create([
            'name' => 'InternTrack Direct',
            'type' => 'direct',
            'adapter_key' => 'direct',
            'is_active' => true,
        ]);

        $extSource = JobSource::create([
            'name' => 'External Jobicy',
            'type' => 'external',
            'adapter_key' => 'jobicy',
            'is_active' => true,
        ]);

        $company = Company::create([
            'name' => 'Internal Test Corp',
            'is_claimed' => true,
            'is_external' => false,
        ]);

        // Direct job with expired deadline
        $directJob = Job::create([
            'company_id' => $company->id,
            'source_id' => $directSource->id,
            'title' => 'Direct Employer Job',
            'description' => 'Important direct employer listing.',
            'job_type' => 'internship',
            'deadline' => now()->subDays(5), // Expired!
            'is_active' => true,
        ]);

        // External job with expired deadline
        $externalJob = Job::create([
            'company_id' => $company->id,
            'source_id' => $extSource->id,
            'source_job_id' => 'EXT-EXPIRED-99',
            'title' => 'External Expired Job',
            'description' => 'External aggregator listing.',
            'job_type' => 'full-time',
            'deadline' => now()->subDays(5), // Expired!
            'is_active' => true,
        ]);

        $this->artisan('jobs:deactivate-stale')
            ->assertExitCode(0);

        $directJob->refresh();
        $externalJob->refresh();

        // CRITICAL: Direct job must NOT be deactivated by external sync
        $this->assertTrue($directJob->is_active, 'Direct employer jobs must never be auto-deactivated');

        // External job MUST be deactivated
        $this->assertFalse($externalJob->is_active, 'External expired jobs must be deactivated');
    }

    /**
     * 19. Verify job URLs command marks 404/410 jobs inactive
     */
    public function test_19_verify_job_urls_deactivates_dead_links(): void
    {
        $extSource = JobSource::create([
            'name' => 'External Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'is_active' => true,
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'is_external' => true,
        ]);

        $deadJob = Job::create([
            'company_id' => $company->id,
            'source_id' => $extSource->id,
            'source_job_id' => 'DEAD-404',
            'title' => 'Gone 404 Job',
            'description' => 'Job that was deleted upstream.',
            'job_url' => 'https://example.com/jobs/deleted-404',
            'is_active' => true,
        ]);

        $aliveJob = Job::create([
            'company_id' => $company->id,
            'source_id' => $extSource->id,
            'source_job_id' => 'ALIVE-200',
            'title' => 'Live 200 Job',
            'description' => 'Job that is still active.',
            'job_url' => 'https://example.com/jobs/active-200',
            'is_active' => true,
        ]);

        Http::fake([
            'https://example.com/jobs/deleted-404' => Http::response('Not Found', 404),
            'https://example.com/jobs/active-200' => Http::response('OK', 200),
        ]);

        $this->artisan('jobs:verify-urls --limit=10')
            ->assertExitCode(0);

        $deadJob->refresh();
        $aliveJob->refresh();

        $this->assertFalse($deadJob->is_active);
        $this->assertTrue($aliveJob->is_active);
    }

    /**
     * 20. Frontend query excludes inactive, expired, blank, and 2024 jobs
     */
    public function test_20_frontend_query_excludes_inactive_expired_and_stale_jobs(): void
    {
        $extSource = JobSource::create([
            'name' => 'External Source',
            'type' => 'external',
            'adapter_key' => 'remotive',
            'is_active' => true,
        ]);

        $company = Company::create([
            'name' => 'Acme Labs',
            'is_external' => true,
        ]);

        // Job 1: Valid active job
        $validJob = Job::create([
            'company_id' => $company->id,
            'source_id' => $extSource->id,
            'source_job_id' => 'VALID-1',
            'title' => 'Valid 2026 Developer Intern',
            'description' => 'Great role.',
            'posted_at' => now()->subDays(5),
            'deadline' => now()->addDays(20),
            'is_active' => true,
        ]);

        // Job 2: Expired deadline
        Job::create([
            'company_id' => $company->id,
            'source_id' => $extSource->id,
            'source_job_id' => 'EXPIRED-2',
            'title' => 'Expired 2026 Intern',
            'description' => 'Deadline passed.',
            'posted_at' => now()->subDays(20),
            'deadline' => now()->subDays(2),
            'is_active' => true,
        ]);

        // Job 3: 2024 Legacy job
        Job::create([
            'company_id' => $company->id,
            'source_id' => $extSource->id,
            'source_job_id' => 'LEGACY-2024',
            'title' => 'Old 2024 Job That Should Never Show',
            'description' => 'From 2024.',
            'posted_at' => '2024-03-01 10:00:00',
            'is_active' => true,
        ]);

        // Job 4: Inactive job
        Job::create([
            'company_id' => $company->id,
            'source_id' => $extSource->id,
            'source_job_id' => 'INACTIVE-4',
            'title' => 'Deactivated Job',
            'description' => 'Not active.',
            'posted_at' => now()->subDays(2),
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->studentUser)->get('/jobs');

        $response->assertStatus(200);
        $response->assertSee('Valid 2026 Developer Intern');
        $response->assertDontSee('Expired 2026 Intern');
        $response->assertDontSee('Old 2024 Job That Should Never Show');
        $response->assertDontSee('Deactivated Job');
    }

    /**
     * 21. Jobicy adapter normalizes salary with salaryPeriod and handles industry array
     */
    public function test_21_jobicy_adapter_normalizes_salary_and_industry(): void
    {
        Http::fake([
            'https://jobicy.com/api/v2/remote-jobs*' => Http::response([
                'jobs' => [
                    [
                        'id' => 7712,
                        'url' => 'https://jobicy.com/jobs/7712',
                        'jobTitle' => 'Remote Laravel Architect',
                        'companyName' => 'Cloudscale Solutions',
                        'jobDescription' => '<p>Architect distributed PHP applications.</p>',
                        'jobGeo' => 'Worldwide',
                        'jobType' => ['Full-Time'],
                        'jobIndustry' => ['Engineering', 'Information Technology'],
                        'pubDate' => now()->subDays(3)->toIso8601String(),
                        'salaryMin' => 90000,
                        'salaryMax' => 120000,
                        'salaryCurrency' => 'USD',
                        'salaryPeriod' => 'yearly',
                    ]
                ]
            ], 200),
        ]);

        $source = JobSource::create([
            'name' => 'Jobicy Test Feed',
            'type' => 'external',
            'adapter_key' => 'jobicy',
            'api_endpoint' => 'https://jobicy.com/api/v2/remote-jobs',
            'auth_type' => 'none',
            'is_active' => true,
        ]);

        $importService = app(JobImportService::class);
        $metrics = $importService->importFromSource($source);

        $this->assertEquals(1, $metrics['inserted']);

        $job = Job::where('source_job_id', '7712')->first();
        $this->assertNotNull($job);
        $this->assertEquals('Remote Laravel Architect', $job->title);
        $this->assertEquals('Cloudscale Solutions', $job->company->name);
        $this->assertEquals('$90,000 - $120,000 / yr', $job->stipend_salary);
        $this->assertStringContainsString('Engineering', $job->skills_required);
    }

    /**
     * 22. Dynamic loading endpoint returns JSON and replaces pages system
     */
    public function test_22_dynamic_loading_endpoint_returns_json_and_removes_page_numbers(): void
    {
        $company = Company::create([
            'name' => 'Dynamic Tech Co',
            'is_claimed' => true,
            'is_external' => false,
        ]);

        // Create 15 jobs to span across batches (page size 12)
        for ($i = 1; $i <= 15; $i++) {
            Job::create([
                'company_id' => $company->id,
                'title' => "Dynamic Opportunity #{$i}",
                'description' => "Description for dynamic opportunity #{$i}",
                'job_type' => 'internship',
                'posted_at' => now()->subDays(1),
                'is_active' => true,
            ]);
        }

        // Standard GET request should render dynamic loader and NO page-number pagination
        $webResponse = $this->actingAs($this->studentUser)->get('/jobs');
        $webResponse->assertStatus(200);
        $webResponse->assertSee('id="jobs-grid"', false);
        $webResponse->assertSee('id="dynamic-loader-area"', false);
        $webResponse->assertSee('id="scroll-sentinel"', false);
        $webResponse->assertDontSee('<ul class="pagination"', false);

        // AJAX dynamic load request for page 2
        $ajaxResponse = $this->actingAs($this->studentUser)->getJson('/jobs?page=2', [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure([
            'html',
            'hasMore',
            'nextPageUrl',
            'currentPage',
            'total',
            'count',
        ]);

        $this->assertTrue($ajaxResponse->json('hasMore')); // Unlimited continuous scrolling enabled
        $this->assertEquals(3, $ajaxResponse->json('count'));
        $this->assertStringContainsString('job-card', $ajaxResponse->json('html'));
        $this->assertStringContainsString('Dynamic Opportunity #', $ajaxResponse->json('html'));
    }
}
