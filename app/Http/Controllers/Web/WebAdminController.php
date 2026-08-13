<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Company;
use App\Models\CompanyVerification;
use App\Models\Job;
use App\Models\User;
use App\Services\Companies\CompanyVerificationService;
use App\Services\Jobs\JobAggregatorService;
use Illuminate\Http\Request;

class WebAdminController extends Controller
{
    public function __construct(
        protected CompanyVerificationService $verificationService,
        protected JobAggregatorService $jobAggregatorService
    ) {}

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalCompanies = Company::count();
        $pendingVerificationsCount = CompanyVerification::where('status', 'pending')->where('is_latest', true)->count();
        $totalJobs = Job::count();
        $externalJobsCount = Job::whereNotNull('source_id')->whereHas('source', function ($q) {
            $q->where('type', 'external');
        })->count();

        $recentLogs = AdminLog::with('admin.user')->orderBy('created_at', 'desc')->limit(6)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'totalCompanies',
            'pendingVerificationsCount',
            'totalJobs',
            'externalJobsCount',
            'recentLogs'
        ));
    }

    public function verifications(Request $request)
    {
        $verifications = CompanyVerification::with(['company', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.verifications', compact('verifications'));
    }

    public function approveVerification(Request $request, string $id)
    {
        $verification = CompanyVerification::findOrFail($id);

        $this->verificationService->approveVerification(
            $verification,
            $request->user(),
            $request->get('remarks', 'Approved by admin console.')
        );

        return back()->with('success', "Verification for company '{$verification->company->name}' approved!");
    }

    public function rejectVerification(Request $request, string $id)
    {
        $request->validate(['remarks' => 'required|string|max:500']);

        $verification = CompanyVerification::findOrFail($id);

        $this->verificationService->rejectVerification(
            $verification,
            $request->user(),
            $request->remarks
        );

        return back()->with('success', "Verification for company '{$verification->company->name}' rejected.");
    }

    public function companies()
    {
        $companies = Company::with(['latestVerification', 'jobs'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.companies', compact('companies'));
    }

    public function jobs()
    {
        $jobs = Job::with(['company', 'source'])->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.jobs', compact('jobs'));
    }

    public function users()
    {
        $users = User::with(['student', 'company'])->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function logs()
    {
        $logs = AdminLog::with('admin.user')->orderBy('created_at', 'desc')->paginate(25);
        return view('admin.logs', compact('logs'));
    }

    public function runAggregator(Request $request)
    {
        $source = $request->get('source', 'sample_external_feed');
        $metrics = $this->jobAggregatorService->ingestFromSource($source);

        return back()->with('success', "Job Aggregator ran for {$source}: {$metrics['inserted']} inserted, {$metrics['duplicated']} updated/duplicated.");
    }
}
