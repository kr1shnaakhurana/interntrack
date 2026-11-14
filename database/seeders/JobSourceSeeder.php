<?php

namespace Database\Seeders;

use App\Models\JobSource;
use Illuminate\Database\Seeder;

class JobSourceSeeder extends Seeder
{
    public function run(): void
    {
        JobSource::updateOrCreate(
            ['name' => 'InternTrack Direct'],
            [
                'type' => 'direct',
                'adapter_key' => null,
                'auth_type' => 'none',
                'is_active' => true,
            ]
        );

        JobSource::updateOrCreate(
            ['name' => 'Adzuna Job Search API'],
            [
                'type' => 'external',
                'adapter_key' => 'adzuna',
                'base_url' => 'https://api.adzuna.com',
                'api_endpoint' => 'https://api.adzuna.com/v1/api/jobs/in/search/1',
                'auth_type' => 'api_key',
                'config' => [
                    'country' => 'in',
                    'default_keyword' => 'internship',
                    'results_per_page' => 20,
                ],
                'is_active' => true,
            ]
        );

        JobSource::updateOrCreate(
            ['name' => 'Remotive Remote Jobs'],
            [
                'type' => 'external',
                'adapter_key' => 'remotive',
                'base_url' => 'https://remotive.com',
                'api_endpoint' => 'https://remotive.com/api/remote-jobs',
                'auth_type' => 'none',
                'config' => [
                    'limit' => 25,
                    'category' => 'software-dev',
                ],
                'is_active' => true,
            ]
        );

        JobSource::updateOrCreate(
            ['name' => 'Jobicy Remote Jobs'],
            [
                'type' => 'external',
                'adapter_key' => 'jobicy',
                'base_url' => 'https://jobicy.com',
                'api_endpoint' => 'https://jobicy.com/api/v2/remote-jobs',
                'auth_type' => 'none',
                'config' => [
                    'count' => 20,
                    'industry' => 'engineering',
                ],
                'is_active' => true,
            ]
        );

        JobSource::updateOrCreate(
            ['name' => 'UPSC & Central Government Recruitment'],
            [
                'type' => 'external',
                'adapter_key' => 'government_rss',
                'base_url' => 'https://www.upsc.gov.in',
                'api_endpoint' => 'https://www.upsc.gov.in/rss.xml',
                'auth_type' => 'none',
                'config' => [
                    'category' => 'Central Government',
                ],
                'is_active' => true,
            ]
        );

        JobSource::updateOrCreate(
            ['name' => 'National Career Service (NCS) Government Feed'],
            [
                'type' => 'external',
                'adapter_key' => 'government_rss',
                'base_url' => 'https://www.ncs.gov.in',
                'api_endpoint' => 'https://www.ncs.gov.in/rss',
                'auth_type' => 'none',
                'config' => [
                    'category' => 'All India Govt Opportunities',
                ],
                'is_active' => true,
            ]
        );

        JobSource::updateOrCreate(
            ['name' => 'Sample External Feed (Mock Demo)'],
            [
                'type' => 'external',
                'adapter_key' => 'sample_external_feed',
                'base_url' => 'https://interntrack.dev/mock-feed',
                'api_endpoint' => null,
                'auth_type' => 'none',
                'is_active' => true,
            ]
        );

        // Explicitly deactivate restricted sources that do not authorize automated scraping
        JobSource::whereIn('name', ['LinkedIn Jobs', 'Internshala', 'Govt Jobs Portal'])
            ->update([
                'is_active' => false,
                'last_fetch_status' => 'disabled',
                'last_fetch_error' => 'Direct website scraping is disabled. Use authorized API or official feeds.',
            ]);
    }
}
