<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Job;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $recentJobs = Job::with(['company.latestVerification', 'source'])
            ->where('is_active', true)
            ->orderBy('posted_at', 'desc')
            ->limit(6)
            ->get();

        $featuredCompanies = Company::with('latestVerification')
            ->whereHas('latestVerification', function ($q) {
                $q->where('status', 'approved');
            })
            ->limit(4)
            ->get();

        return view('welcome', compact('recentJobs', 'featuredCompanies'));
    }
}
