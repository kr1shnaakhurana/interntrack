<?php

namespace App\Services\Jobs;

use App\Models\Company;

class CompanyResolutionService
{
    /**
     * Resolve an existing company or create an external placeholder company.
     */
    public function resolveCompany(string $companyName, ?string $website = null, ?string $location = null): Company
    {
        $normalizedName = trim($companyName);

        // 1. Attempt exact match by name
        $company = Company::whereRaw('LOWER(name) = ?', [strtolower($normalizedName)])->first();

        if ($company) {
            return $company;
        }

        // 2. Determine sector heuristically
        $sector = 'private';
        $lowerName = strtolower($normalizedName);
        if (str_contains($lowerName, 'government') || str_contains($lowerName, 'nic') || str_contains($lowerName, 'ministry') || str_contains($lowerName, 'gov')) {
            $sector = 'government';
        } elseif (str_contains($lowerName, 'foundation') || str_contains($lowerName, 'ngo') || str_contains($lowerName, 'trust')) {
            $sector = 'ngo';
        }

        // 3. Create external placeholder company
        return Company::create([
            'name' => $normalizedName,
            'website' => $website,
            'location' => $location,
            'is_claimed' => false,
            'is_external' => true,
            'sector' => $sector,
        ]);
    }
}
