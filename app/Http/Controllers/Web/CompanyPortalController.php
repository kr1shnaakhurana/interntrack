<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobSource;
use App\Services\Companies\CompanyVerificationService;

use Illuminate\Http\Request;

class CompanyPortalController extends Controller
{
    public function __construct(
        protected CompanyVerificationService $verificationService
    ) {}

    protected function getCompany(Request $request): Company
    {
        $user = $request->user();
        $company = $user->company;

        if (! $company) {
            $company = Company::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'is_claimed' => true,
                'is_external' => false,
                'sector' => 'private',
            ]);
        }

        return $company;
    }

    public function dashboard(Request $request)
    {
        $company = $this->getCompany($request);
        $company->load('latestVerification');

        $activeJobsCount = Job::where('company_id', $company->id)->where('is_active', true)->count();
        $totalJobsCount = Job::where('company_id', $company->id)->count();
        $applicationsCount = Application::whereHas('job', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->count();

        $recentJobs = Job::where('company_id', $company->id)->orderBy('created_at', 'desc')->limit(5)->get();

        return view('company.dashboard', compact(
            'company',
            'activeJobsCount',
            'totalJobsCount',
            'applicationsCount',
            'recentJobs'
        ));
    }

    public function verification(Request $request)
    {
        $company = $this->getCompany($request);
        $verifications = $company->verifications()->orderBy('created_at', 'desc')->get();

        return view('company.verification', compact('company', 'verifications'));
    }

    public function submitVerification(Request $request)
    {
        $company = $this->getCompany($request);

        $request->validate([
            'documents_url' => 'required|url|max:500',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $this->verificationService->requestVerification(
            $company,
            $request->documents_url,
            $request->remarks
        );

        return redirect('/company/verification')->with('success', 'Verification request submitted for admin review!');
    }

    public function jobs(Request $request)
    {
        $company = $this->getCompany($request);
        $jobs = Job::with('applications')->where('company_id', $company->id)->orderBy('created_at', 'desc')->paginate(10);

        return view('company.jobs.index', compact('company', 'jobs'));
    }

    public function createJob(Request $request)
    {
        $company = $this->getCompany($request);
        $company->load('latestVerification');

        // VERIFICATION GATE ENFORCEMENT
        if (! $company->isVerified()) {
            return redirect('/company/verification')->with('warning', 'Your company must be verified before you can publish opportunities.');
        }

        return view('company.jobs.create', compact('company'));
    }

    public function storeJob(Request $request)
    {
        $company = $this->getCompany($request);

        // VERIFICATION GATE ENFORCEMENT
        if (! $company->isVerified()) {
            return redirect('/company/verification')->with('error', 'Publishing blocked. Unverified company profile.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'job_type' => 'required|string|in:internship,full-time,part-time,research,fellowship,government,other',
            'location' => 'nullable|string|max:255',
            'stipend_salary' => 'nullable|string|max:100',
            'deadline' => 'nullable|date',
            'skills_required' => 'nullable|string',
        ]);

        $directSource = JobSource::firstOrCreate(
            ['name' => 'InternTrack Direct'],
            ['type' => 'direct', 'is_active' => true]
        );

        Job::create([
            'company_id' => $company->id,
            'source_id' => $directSource->id,
            'title' => $request->title,
            'description' => $request->description,
            'job_type' => $request->job_type,
            'location' => $request->location,
            'is_remote' => $request->boolean('is_remote'),
            'stipend_salary' => $request->stipend_salary,
            'deadline' => $request->deadline,
            'skills_required' => $request->skills_required,
            'is_active' => true,
            'posted_at' => now(),
        ]);

        return redirect('/company/jobs')->with('success', 'Job opportunity published successfully!');
    }

    public function editJob(Request $request, string $id)
    {
        $company = $this->getCompany($request);
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        return view('company.jobs.edit', compact('company', 'job'));
    }

    public function updateJob(Request $request, string $id)
    {
        $company = $this->getCompany($request);
        $job = Job::where('company_id', $company->id)->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'job_type' => 'required|string|in:internship,full-time,part-time,research,fellowship,government,other',
            'location' => 'nullable|string|max:255',
            'stipend_salary' => 'nullable|string|max:100',
            'deadline' => 'nullable|date',
            'skills_required' => 'nullable|string',
        ]);

        $job->update($request->only([
            'title', 'description', 'job_type', 'location', 'is_remote', 'stipend_salary', 'deadline', 'skills_required'
        ]));

        return redirect('/company/jobs')->with('success', 'Job opportunity updated successfully.');
    }

    public function applications(Request $request)
    {
        $company = $this->getCompany($request);

        $applications = Application::with(['student.user', 'job', 'interviews', 'statusHistory'])
            ->whereHas('job', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('company.applications', compact('company', 'applications'));
    }
}
