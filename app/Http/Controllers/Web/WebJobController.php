<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\SavedJob;
use App\Models\Skill;
use App\Services\Applications\ApplicationService;
use Illuminate\Http\Request;

class WebJobController extends Controller
{
    public function __construct(
        protected ApplicationService $applicationService
    ) {}

    public function index(Request $request)
    {
        $query = Job::with(['company.latestVerification', 'source', 'skills'])
            ->where('is_active', true);

        if ($request->filled('keyword')) {
            $kw = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', $kw)
                  ->orWhere('description', 'like', $kw)
                  ->orWhere('skills_required', 'like', $kw);
            });
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->has('is_remote') && $request->is_remote !== '') {
            $query->where('is_remote', filter_var($request->is_remote, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('sector')) {
            $sec = $request->sector;
            $query->whereHas('company', function ($q) use ($sec) {
                $q->where('sector', $sec);
            });
        }

        if ($request->boolean('verified_only')) {
            $query->whereHas('company.verifications', function ($q) {
                $q->where('is_latest', true)->where('status', 'approved');
            });
        }

        $jobs = $query->orderBy('posted_at', 'desc')->paginate(12);
        $skills = Skill::orderBy('name', 'asc')->get();

        return view('jobs.index', compact('jobs', 'skills'));
    }

    public function show(string $id)
    {
        $job = Job::with(['company.latestVerification', 'source', 'skills'])->findOrFail($id);

        $hasApplied = false;
        $isSaved = false;

        if (auth()->check() && auth()->user()->isStudent() && auth()->user()->student) {
            $studentId = auth()->user()->student->id;
            $hasApplied = \App\Models\Application::where('student_id', $studentId)->where('job_id', $job->id)->exists();
            $isSaved = SavedJob::where('student_id', $studentId)->where('job_id', $job->id)->exists();
        }

        return view('jobs.show', compact('job', 'hasApplied', 'isSaved'));
    }

    public function saveJob(Request $request, string $id)
    {
        $student = $request->user()->student;
        if (! $student) {
            return back()->with('error', 'Student profile required.');
        }

        $job = Job::findOrFail($id);

        SavedJob::firstOrCreate([
            'student_id' => $student->id,
            'job_id' => $job->id,
        ], [
            'saved_at' => now(),
        ]);

        return back()->with('success', 'Job saved to your bookmarks!');
    }

    public function applyJob(Request $request, string $id)
    {
        $student = $request->user()->student;
        if (! $student) {
            return back()->with('error', 'Student profile required.');
        }

        $job = Job::findOrFail($id);

        try {
            $this->applicationService->apply($student, $job, $request->only([
                'cover_letter_url', 'expected_salary', 'current_ctc', 'notes'
            ]));

            return redirect('/applications')->with('success', 'Application submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
