<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Interview;
use App\Models\Job;
use App\Models\SavedJob;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;

        if (! $student) {
            $student = \App\Models\Student::create(['user_id' => $request->user()->id]);
        }

        $totalApplications = Application::where('student_id', $student->id)->count();
        $inProgressApplications = Application::where('student_id', $student->id)
            ->whereIn('status', ['applied', 'shortlisted', 'interview'])
            ->count();
        
        $upcomingInterviewsCount = Interview::whereHas('application', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->where('result', 'pending')->count();

        $savedJobsCount = SavedJob::where('student_id', $student->id)->count();

        $recentJobs = Job::with(['company.latestVerification', 'source'])
            ->where('is_active', true)
            ->orderBy('posted_at', 'desc')
            ->limit(6)
            ->get();

        return view('dashboard.index', compact(
            'student',
            'totalApplications',
            'inProgressApplications',
            'upcomingInterviewsCount',
            'savedJobsCount',
            'recentJobs'
        ));
    }
}
