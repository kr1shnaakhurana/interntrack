<?php

namespace App\Services\Jobs\Sources;

use Carbon\Carbon;

class GovernmentFeedAdapter extends RssXmlAdapter
{
    public function getSourceIdentifier(): string
    {
        return $this->sourceIdentifier ?? 'government_rss';
    }

    public function getSourceName(): string
    {
        return $this->sourceName ?? 'Official Government Recruitment Feed';
    }

    public function normalizeJob(array $rawItem): array
    {
        $title = trim(strip_tags($rawItem['title'] ?? ''));
        $rawDescription = $rawItem['description'] ?? '';
        $description = $this->normalizationService->sanitizeDescription($rawDescription);
        $plainTextDesc = strip_tags($rawDescription);
        $combinedText = $title . ' ' . $plainTextDesc;

        // 1. Identify organization & department
        $org = $this->extractOrganization($title, $rawItem['channel'] ?? '', $rawItem['author'] ?? '');

        // 2. Classify Government Category
        $govCategory = $this->classifyGovernmentCategory($combinedText, $org);

        // 3. Extract vacancy count
        $vacancies = $this->extractVacancies($combinedText);

        // 4. Extract eligibility & qualifications
        $qualifications = $this->extractQualifications($combinedText);

        // 5. Extract age limit
        $ageLimit = $this->extractAgeLimit($combinedText);

        // 6. Extract deadline
        $deadline = $this->extractDeadline($combinedText);

        // 7. Extract pay scale / salary
        $payScale = $this->extractPayScale($combinedText);

        // 8. Determine post name
        $postName = $this->extractPostName($title, $org);

        // 9. Job type
        $jobType = 'government';
        if (str_contains(strtolower($title), 'intern') || str_contains(strtolower($title), 'trainee') || str_contains(strtolower($title), 'apprentice')) {
            $jobType = 'internship';
        }

        $jobUrl = $this->normalizationService->validateUrl($rawItem['link'] ?? null);
        $postedAt = $this->normalizationService->normalizeDate($rawItem['pubDate'] ?? null);

        return [
            'company_name' => $org,
            'company_website' => null,
            'title' => $title,
            'description' => $description,
            'job_type' => $jobType,
            'location' => $this->extractLocation($combinedText),
            'is_remote' => false,
            'stipend_salary' => $payScale,
            'deadline' => $deadline,
            'skills_required' => $qualifications,
            'job_url' => $jobUrl,
            'source_job_id' => (string) ($rawItem['id'] ?? md5($title . $jobUrl)),
            'posted_at' => $postedAt,
            'metadata' => [
                'is_government' => true,
                'source_platform' => 'Official Government Recruitment Portal',
                'government_category' => $govCategory,
                'department' => $org,
                'post_name' => $postName,
                'vacancies_count' => $vacancies,
                'eligibility_criteria' => $qualifications,
                'age_limit' => $ageLimit,
                'official_notification_url' => $jobUrl,
                'official_application_url' => $jobUrl,
                'attribution' => 'Official Public Recruitment Notification',
            ],
        ];
    }

    /**
     * Identify official organization or department.
     */
    protected function extractOrganization(string $title, string $channel, string $author): string
    {
        $patterns = [
            '/Union Public Service Commission|UPSC/i' => 'Union Public Service Commission (UPSC)',
            '/Staff Selection Commission|SSC/i' => 'Staff Selection Commission (SSC)',
            '/Railway Recruitment Board|RRB|Indian Railways/i' => 'Railway Recruitment Board (RRB)',
            '/Institute of Banking Personnel Selection|IBPS/i' => 'Institute of Banking Personnel Selection (IBPS)',
            '/State Bank of India|SBI/i' => 'State Bank of India (SBI)',
            '/Reserve Bank of India|RBI/i' => 'Reserve Bank of India (RBI)',
            '/Defence Research and Development Organisation|DRDO/i' => 'DRDO (Ministry of Defence)',
            '/Indian Space Research Organisation|ISRO/i' => 'Indian Space Research Organisation (ISRO)',
            '/National Informatics Centre|NIC/i' => 'National Informatics Centre (NIC)',
            '/Bhabha Atomic Research Centre|BARC/i' => 'BARC (Dept of Atomic Energy)',
            '/Indian Army/i' => 'Indian Army (Ministry of Defence)',
            '/Indian Navy/i' => 'Indian Navy (Ministry of Defence)',
            '/Indian Air Force|IAF/i' => 'Indian Air Force (Ministry of Defence)',
            '/Steel Authority of India|SAIL/i' => 'Steel Authority of India (SAIL)',
            '/Bharat Heavy Electricals|BHEL/i' => 'Bharat Heavy Electricals Limited (BHEL)',
            '/Oil and Natural Gas Corporation|ONGC/i' => 'ONGC India',
            '/Indian Oil Corporation|IOCL/i' => 'Indian Oil Corporation (IOCL)',
            '/National Thermal Power Corporation|NTPC/i' => 'NTPC Limited',
            '/Bharat Petroleum|BPCL/i' => 'BPCL',
            '/Coal India|CIL/i' => 'Coal India Limited',
        ];

        foreach ($patterns as $pattern => $name) {
            if (preg_match($pattern, $title) || preg_match($pattern, $channel) || preg_match($pattern, $author)) {
                return $name;
            }
        }

        if (! empty($author) && ! str_contains(strtolower($author), 'admin')) {
            return trim($author);
        }

        if (! empty($channel) && ! str_contains(strtolower($channel), 'feed')) {
            return trim($channel);
        }

        // Try extracting department from title (e.g. "Ministry of Health Recruitment ...")
        if (preg_match('/(Ministry of [a-zA-Z\s]+|Department of [a-zA-Z\s]+)/i', $title, $matches)) {
            return trim($matches[1]);
        }

        return 'Government Department / Commission';
    }

    /**
     * Classify recruitment into standardized categories.
     */
    protected function classifyGovernmentCategory(string $text, string $org): string
    {
        $lower = strtolower($text . ' ' . $org);

        if (str_contains($lower, 'defence') || str_contains($lower, 'defense') || str_contains($lower, 'army') || str_contains($lower, 'navy') || str_contains($lower, 'air force') || str_contains($lower, 'nda') || str_contains($lower, 'cds') || str_contains($lower, 'drdo')) {
            return 'Defence';
        }
        if (str_contains($lower, 'railway') || str_contains($lower, 'rrb') || str_contains($lower, 'loco pilot')) {
            return 'Railway';
        }
        if (str_contains($lower, 'bank') || str_contains($lower, 'banking') || str_contains($lower, 'ibps') || str_contains($lower, 'sbi') || str_contains($lower, 'rbi') || preg_match('/\b(po|clerk)\b/i', $text)) {
            return 'Banking';
        }
        if (str_contains($lower, 'psu') || str_contains($lower, 'ongc') || str_contains($lower, 'bhel') || str_contains($lower, 'iocl') || str_contains($lower, 'sail') || str_contains($lower, 'ntpc') || str_contains($lower, 'coal india')) {
            return 'PSU';
        }
        if (str_contains($lower, 'upsc') || str_contains($lower, 'ssc') || str_contains($lower, 'central government') || str_contains($lower, 'civil services')) {
            return 'Central Government';
        }
        if (str_contains($lower, 'state psc') || str_contains($lower, 'mppsc') || str_contains($lower, 'uppsc') || str_contains($lower, 'bpsc') || str_contains($lower, 'kpsc') || str_contains($lower, 'tnpsc') || str_contains($lower, 'wbpsc') || str_contains($lower, 'state government')) {
            return 'State Government';
        }
        if (str_contains($lower, 'university') || str_contains($lower, 'college') || str_contains($lower, 'professor') || str_contains($lower, 'teacher') || str_contains($lower, 'iit') || str_contains($lower, 'nit')) {
            return 'Education';
        }
        if (str_contains($lower, 'apprentice') || str_contains($lower, 'apprenticeship')) {
            return 'Apprenticeship';
        }
        if (str_contains($lower, 'internship') || str_contains($lower, 'intern')) {
            return 'Government Internship';
        }

        return 'Central Government';
    }

    /**
     * Extract number of vacancies if present.
     */
    protected function extractVacancies(string $text): ?string
    {
        if (preg_match('/(?:total\s*)?(\d{1,6})\s*(?:vacanc(?:ies|y)|posts?|openings?|seats?)/i', $text, $matches)) {
            return $matches[1] . ' Posts';
        }
        return null;
    }

    /**
     * Extract qualifications mentioned in the notification text.
     */
    protected function extractQualifications(string $text): ?string
    {
        $found = [];
        $degrees = [
            'B.E' => 'B.E/B.Tech',
            'B.Tech' => 'B.E/B.Tech',
            'M.Tech' => 'M.E/M.Tech',
            'MCA' => 'MCA',
            'BCA' => 'BCA',
            'MBA' => 'MBA',
            'B.Sc' => 'B.Sc',
            'M.Sc' => 'M.Sc',
            'Graduate' => 'Bachelor\'s Degree',
            'Graduation' => 'Bachelor\'s Degree',
            'Post Graduate' => 'Post Graduation',
            'Diploma' => 'Diploma',
            '10th' => '10th Pass',
            '12th' => '12th Pass / Intermediate',
            'MBBS' => 'MBBS',
            'LLB' => 'Law (LLB)',
            'Chartered Accountant' => 'CA / ICWA',
        ];

        foreach ($degrees as $needle => $label) {
            if (preg_match('/\b' . preg_quote($needle, '/') . '\b/i', $text)) {
                $found[] = $label;
            }
        }

        return ! empty($found) ? implode(', ', array_unique($found)) : 'Check Official Notification';
    }

    /**
     * Extract age limit from notification.
     */
    protected function extractAgeLimit(string $text): ?string
    {
        if (preg_match('/(?:age\s*limit|age):\s*([0-9]{2}\s*(?:to|-)\s*[0-9]{2}\s*years?)/i', $text, $matches)) {
            return $matches[1];
        }
        if (preg_match('/(?:max(?:imum)?\s*age|upper\s*age\s*limit):\s*([0-9]{2}\s*years?)/i', $text, $matches)) {
            return 'Max ' . $matches[1];
        }
        return null;
    }

    /**
     * Extract deadline date if available.
     */
    protected function extractDeadline(string $text): ?string
    {
        if (preg_match('/(?:last\s*date|apply\s*(?:before|by)|closing\s*date)[:\s]+([0-9]{1,2}[-\/\.][0-9]{1,2}[-\/\.][0-9]{2,4})/i', $text, $matches)) {
            try {
                return Carbon::parse($matches[1])->toDateTimeString();
            } catch (\Exception) {
                return null;
            }
        }
        return null;
    }

    /**
     * Extract pay scale / salary.
     */
    protected function extractPayScale(string $text): ?string
    {
        if (preg_match('/(Level\s*[0-9]{1,2}\s*(?:Pay\s*Matrix)?)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        if (preg_match('/(?:pay\s*scale|salary|stipend)[:\s]+(?:rs\.?|inr|₹)?\s*([0-9,]+(?:\s*-\s*[0-9,]+)?(?:\s*\/\s*(?:month|year|pm))?)/i', $text, $matches)) {
            return '₹' . trim($matches[1]);
        }
        return 'As Per Government Rules';
    }

    /**
     * Clean and extract post name.
     */
    protected function extractPostName(string $title, string $org): string
    {
        $cleaned = str_ireplace($org, '', $title);
        $cleaned = preg_replace('/recruitment\s*(?:202[0-9])?/i', '', $cleaned);
        $cleaned = preg_replace('/notification|apply online|\d+\s*posts/i', '', $cleaned);
        $cleaned = trim(trim($cleaned), ':-–| ');
        return ! empty($cleaned) ? $cleaned : $title;
    }

    /**
     * Extract location or default to All India.
     */
    protected function extractLocation(string $text): string
    {
        $states = [
            'Delhi', 'Maharashtra', 'Karnataka', 'Tamil Nadu', 'Uttar Pradesh',
            'Gujarat', 'West Bengal', 'Telangana', 'Rajasthan', 'Kerala',
            'Punjab', 'Haryana', 'Bihar', 'Odisha', 'Assam', 'Madhya Pradesh',
        ];

        foreach ($states as $state) {
            if (preg_match('/\b' . preg_quote($state, '/') . '\b/i', $text)) {
                return $state . ', India';
            }
        }

        return 'All India (Central / State Posting)';
    }
}
