<?php

namespace App\Services\Jobs\Sources;

class SampleFeedAdapter implements JobSourceAdapterInterface
{
    public function getSourceIdentifier(): string
    {
        return 'sample_external_feed';
    }

    public function fetchJobs(): array
    {
        // Simulated permitted external feed payload
        return [
            [
                'ext_id' => 'TCS-INT-2026-001',
                'employer_name' => 'Tata Consultancy Services',
                'employer_website' => 'https://tcs.com',
                'role_title' => 'Software Developer Intern',
                'job_description' => 'Work on cloud backend systems in Laravel & Python.',
                'type' => 'internship',
                'city' => 'Bengaluru, India',
                'remote_work' => true,
                'stipend' => '₹45,000 / month',
                'expire_date' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'skills' => 'PHP, Laravel, PostgreSQL, Docker',
                'url' => 'https://careers.tcs.com/jobs/TCS-INT-2026-001',
            ],
            [
                'ext_id' => 'INFY-RF-2026-092',
                'employer_name' => 'Infosys Labs',
                'employer_website' => 'https://infosys.com',
                'role_title' => 'AI Research Fellow',
                'job_description' => 'Research on large language models and multi-agent systems.',
                'type' => 'research',
                'city' => 'Hyderabad, India',
                'remote_work' => false,
                'stipend' => '₹60,000 / month',
                'expire_date' => date('Y-m-d H:i:s', strtotime('+45 days')),
                'skills' => 'Python, PyTorch, Machine Learning',
                'url' => 'https://careers.infosys.com/fellowships/092',
            ],
            [
                'ext_id' => 'NIC-GOV-2026-104',
                'employer_name' => 'National Informatics Centre (NIC)',
                'employer_website' => 'https://nic.in',
                'role_title' => 'Junior Government Tech Fellow',
                'job_description' => 'Digital public infrastructure development for e-governance applications.',
                'type' => 'government',
                'city' => 'New Delhi, India',
                'remote_work' => false,
                'stipend' => '₹50,000 / month',
                'expire_date' => date('Y-m-d H:i:s', strtotime('+60 days')),
                'skills' => 'Java, Spring Boot, PostgreSQL, Linux',
                'url' => 'https://nic.in/recruitment/2026/104',
            ],
        ];
    }

    public function normalizeJob(array $rawItem): array
    {
        return [
            'company_name' => trim($rawItem['employer_name'] ?? 'External Company'),
            'company_website' => $rawItem['employer_website'] ?? null,
            'title' => trim($rawItem['role_title'] ?? ''),
            'description' => trim($rawItem['job_description'] ?? ''),
            'job_type' => strtolower($rawItem['type'] ?? 'internship'),
            'location' => $rawItem['city'] ?? null,
            'is_remote' => (bool) ($rawItem['remote_work'] ?? false),
            'stipend_salary' => $rawItem['stipend'] ?? null,
            'deadline' => $rawItem['expire_date'] ?? null,
            'skills_required' => $rawItem['skills'] ?? null,
            'job_url' => $rawItem['url'] ?? null,
            'source_job_id' => (string) ($rawItem['ext_id'] ?? ''),
        ];
    }

    public function validateJob(array $normalizedItem): bool
    {
        return ! empty($normalizedItem['title'])
            && ! empty($normalizedItem['company_name'])
            && ! empty($normalizedItem['description']);
    }
}
