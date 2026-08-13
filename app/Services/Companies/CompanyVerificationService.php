<?php

namespace App\Services\Companies;

use App\Models\Admin;
use App\Models\AdminLog;
use App\Models\Company;
use App\Models\CompanyVerification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class CompanyVerificationService
{
    /**
     * Submit a new verification request for a company.
     */
    public function requestVerification(Company $company, ?string $documentsUrl, ?string $remarks = null): CompanyVerification
    {
        return DB::transaction(function () use ($company, $documentsUrl, $remarks) {
            // Mark existing verifications as not latest
            CompanyVerification::where('company_id', $company->id)
                ->update(['is_latest' => false]);

            return CompanyVerification::create([
                'company_id' => $company->id,
                'status' => 'pending',
                'documents_url' => $documentsUrl,
                'remarks' => $remarks,
                'applied_at' => now(),
                'is_latest' => true,
            ]);
        });
    }

    /**
     * Approve a verification attempt (Admin action).
     */
    public function approveVerification(CompanyVerification $verification, User $adminUser, ?string $remarks = null): CompanyVerification
    {
        return DB::transaction(function () use ($verification, $adminUser, $remarks) {
            $verification->update([
                'status' => 'approved',
                'verified_by' => $adminUser->id,
                'remarks' => $remarks ?? $verification->remarks,
                'reviewed_at' => now(),
            ]);

            // Update company claim & verification status
            $company = $verification->company;
            $company->update([
                'is_claimed' => true,
                'is_external' => false,
            ]);

            // Log admin action
            $admin = Admin::where('user_id', $adminUser->id)->first();
            if ($admin) {
                AdminLog::create([
                    'admin_id' => $admin->id,
                    'action' => 'company_verification_approved',
                    'entity_type' => 'Company',
                    'entity_id' => $company->id,
                    'description' => "Approved verification for company: {$company->name}",
                ]);
            }

            return $verification;
        });
    }

    /**
     * Reject a verification attempt (Admin action).
     */
    public function rejectVerification(CompanyVerification $verification, User $adminUser, string $remarks): CompanyVerification
    {
        return DB::transaction(function () use ($verification, $adminUser, $remarks) {
            $verification->update([
                'status' => 'rejected',
                'verified_by' => $adminUser->id,
                'remarks' => $remarks,
                'reviewed_at' => now(),
            ]);

            $company = $verification->company;

            $admin = Admin::where('user_id', $adminUser->id)->first();
            if ($admin) {
                AdminLog::create([
                    'admin_id' => $admin->id,
                    'action' => 'company_verification_rejected',
                    'entity_type' => 'Company',
                    'entity_id' => $company->id,
                    'description' => "Rejected verification for company: {$company->name}. Reason: {$remarks}",
                ]);
            }

            return $verification;
        });
    }
}
