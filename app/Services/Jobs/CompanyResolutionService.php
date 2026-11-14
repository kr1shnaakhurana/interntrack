<?php

namespace App\Services\Jobs;

use App\Models\Company;

class CompanyResolutionService
{
    public function __construct(
        protected CompanyMappingService $companyMappingService
    ) {}

    /**
     * Resolve an existing company or create an external placeholder company.
     */
    public function resolveCompany(string $companyName, ?string $website = null, ?string $location = null): Company
    {
        return $this->companyMappingService->resolveCompany($companyName, $website, $location);
    }
}
