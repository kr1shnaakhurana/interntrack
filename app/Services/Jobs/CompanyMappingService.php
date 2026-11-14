<?php

namespace App\Services\Jobs;

use App\Models\Company;

class CompanyMappingService
{
    /**
     * Resolve an existing company or create a safe external company record.
     */
    public function resolveCompany(
        string $companyName,
        ?string $website = null,
        ?string $location = null,
        ?string $logoUrl = null,
        ?string $sector = null
    ): Company {
        $cleanName = trim($companyName);
        if (empty($cleanName) || in_array(strtolower($cleanName), ['n/a', 'unknown', 'null', 'none', 'undefined'])) {
            throw new \InvalidArgumentException('Company name cannot be empty or invalid.');
        }

        // 1. Try matching by website domain if website is provided
        if (! empty($website)) {
            $domain = $this->extractDomain($website);
            if ($domain) {
                $company = Company::where('website', 'like', "%{$domain}%")->first();
                if ($company) {
                    return $this->updateExternalCompanyIfNeeded($company, $location, $logoUrl);
                }
            }
        }

        // 2. Try exact case-insensitive match on company name
        $company = Company::whereRaw('LOWER(name) = ?', [strtolower($cleanName)])->first();
        if ($company) {
            return $this->updateExternalCompanyIfNeeded($company, $location, $logoUrl, $website);
        }

        // 3. Infer sector if not provided
        $detectedSector = $sector ?? $this->detectSector($cleanName);

        // 4. Create new external company without an authenticated user account
        return Company::create([
            'user_id' => null,
            'name' => $cleanName,
            'website' => $website,
            'location' => $location,
            'logo_url' => $logoUrl,
            'is_claimed' => false,
            'is_external' => true,
            'sector' => $detectedSector,
        ]);
    }

    /**
     * Safely update missing fields on external unclaimed companies without overwriting claimed profiles.
     */
    protected function updateExternalCompanyIfNeeded(Company $company, ?string $location = null, ?string $logoUrl = null, ?string $website = null): Company
    {
        // Never touch claimed or internal employer profiles
        if ($company->is_claimed || ! $company->is_external) {
            return $company;
        }

        $updates = [];
        if (empty($company->location) && ! empty($location)) {
            $updates['location'] = $location;
        }
        if (empty($company->logo_url) && ! empty($logoUrl)) {
            $updates['logo_url'] = $logoUrl;
        }
        if (empty($company->website) && ! empty($website)) {
            $updates['website'] = $website;
        }

        if (! empty($updates)) {
            $company->update($updates);
        }

        return $company;
    }

    /**
     * Extract clean root domain for domain matching.
     */
    public function extractDomain(string $url): ?string
    {
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? null;
        if (! $host) {
            return null;
        }

        $host = strtolower(trim($host));
        return preg_replace('/^www\./', '', $host);
    }

    /**
     * Heuristically detect company sector from organization name.
     */
    public function detectSector(string $name): string
    {
        $lower = strtolower($name);

        $govtKeywords = [
            'government', 'ministry', 'department', 'upsc', 'ssc', 'rrb', 'drdo',
            'isro', 'nic', 'commission', 'police', 'sarkari', 'recruitment board',
            'public service', 'railway', 'indian army', 'indian navy', 'indian air force',
            'ibps', 'state bank of india', 'reserve bank of india', 'aiims', 'iit',
            'nit', 'psu', 'bhel', 'ongc', 'ntpc', 'iocl', 'sail', 'hal', 'bel',
        ];

        foreach ($govtKeywords as $kw) {
            if (str_contains($lower, $kw)) {
                return 'government';
            }
        }

        $ngoKeywords = ['ngo', 'foundation', 'trust', 'charity', 'society', 'unicef', 'red cross'];
        foreach ($ngoKeywords as $kw) {
            if (str_contains($lower, $kw)) {
                return 'ngo';
            }
        }

        return 'private';
    }
}

