<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobSource;
use App\Models\SavedJob;
use App\Models\Skill;
use App\Services\Applications\ApplicationService;
use App\Services\Jobs\JobImportService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class WebJobController extends Controller
{
    public function __construct(
        protected ApplicationService $applicationService,
        protected ?JobImportService $jobImportService = null
    ) {
        $this->jobImportService = $jobImportService ?? app(JobImportService::class);
    }

    public function index(Request $request)
    {
        // Auto-refresh feed if stale or explicitly triggered
        if ($request->boolean('refresh') || (! $request->ajax() && (int) $request->input('page', 1) === 1)) {
            $this->refreshExternalFeedsIfNeeded($request->boolean('refresh'));
        }

        $maxAgeDays = (int) config('services.job_fetch.max_age_days', 90);
        $cutoff = now()->subDays($maxAgeDays);

        $query = Job::with(['company.latestVerification', 'source', 'skills'])
            ->where('is_active', true)
            ->whereNotNull('title')
            ->where('title', '!=', '')
            ->whereHas('company')
            ->where(function ($q) {
                // Exclude jobs with past deadlines
                $q->whereNull('deadline')
                  ->orWhere('deadline', '>', now());
            })
            ->where(function ($q) {
                // Never display jobs from 2024 or older in default active listings
                $q->whereNull('posted_at')
                  ->orWhere('posted_at', '>=', '2025-01-01 00:00:00');
            })
            ->where(function ($q) use ($cutoff) {
                // Direct jobs remain visible; external jobs without deadline must be within freshness window
                $q->whereHas('source', fn($sq) => $sq->where('type', 'direct'))
                  ->orWhereNull('source_id')
                  ->orWhere(function ($eq) use ($cutoff) {
                      $eq->whereNotNull('deadline')
                         ->orWhere(function ($nq) use ($cutoff) {
                             $nq->whereNull('deadline')
                                ->where('posted_at', '>=', $cutoff);
                         });
                  });
            });

        if ($request->filled('keyword')) {
            $kw = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', $kw)
                  ->orWhere('description', 'like', $kw)
                  ->orWhere('skills_required', 'like', $kw)
                  ->orWhereHas('company', function ($cq) use ($kw) {
                      $cq->where('name', 'like', $kw);
                  });
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

        if ($request->filled('government_category')) {
            $gCat = $request->government_category;
            $query->whereRaw("metadata->>'government_category' = ?", [$gCat]);
        }

        if ($request->filled('source_type')) {
            if ($request->source_type === 'direct') {
                $query->whereHas('source', fn($sq) => $sq->where('type', 'direct'));
            } elseif ($request->source_type === 'external') {
                $query->whereHas('source', fn($sq) => $sq->where('type', 'external'));
            }
        }

        if ($request->boolean('verified_only')) {
            $query->whereHas('company.verifications', function ($q) {
                $q->where('is_latest', true)->where('status', 'approved');
            });
        }

        $perPage = 12;
        $page = max((int) $request->input('page', 1), 1);
        $totalJobs = (clone $query)->count();
        $lastPage = max((int) ceil($totalJobs / $perPage), 1);

        // If user scrolls past current DB jobs in directory, try replenishing from live sources
        if ($page > $lastPage && $totalJobs > 0) {
            $this->refreshExternalFeedsIfNeeded(true);
            $newTotal = (clone $query)->count();
            if ($newTotal > $totalJobs) {
                $totalJobs = $newTotal;
                $lastPage = max((int) ceil($totalJobs / $perPage), 1);
            }
        }

        // Unlimited continuous feed: cycle through opportunities with wrapping offset so scrolling never halts
        if ($page > $lastPage && $totalJobs > 0) {
            $offset = (($page - 1) * $perPage) % $totalJobs;
            $items = (clone $query)
                ->orderBy('created_at', 'desc')
                ->orderBy('posted_at', 'desc')
                ->skip($offset)
                ->take($perPage)
                ->get();

            $jobs = new LengthAwarePaginator(
                $items,
                $totalJobs * 50, // Virtual unlimited count to allow indefinite continuous scrolling
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $jobs = $query->orderBy('created_at', 'desc')->orderBy('posted_at', 'desc')->paginate($perPage)->withQueryString();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('jobs._job_cards', compact('jobs'))->render(),
                'hasMore' => true, // Always true for unlimited scrolling
                'nextPageUrl' => $jobs->hasMorePages() ? $jobs->nextPageUrl() : url('/jobs?page=' . ($page + 1) . '&' . http_build_query($request->except('page'))),
                'currentPage' => $jobs->currentPage(),
                'total' => $totalJobs,
                'count' => $jobs->count(),
            ]);
        }

        $skills = Skill::orderBy('name', 'asc')->get();
        $jobSources = JobSource::where('is_active', true)->get();
        $govCategories = [
            'Central Government',
            'State Government',
            'PSU',
            'Railway',
            'Banking',
            'Defence',
            'Education',
            'Apprenticeship',
            'Government Internship',
        ];

        return view('jobs.index', compact('jobs', 'skills', 'jobSources', 'govCategories'));
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

        $isExternal = ($job->source && $job->source->type === 'external') || $job->company?->is_external;

        return view('jobs.show', compact('job', 'hasApplied', 'isSaved', 'isExternal'));
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

        // For external jobs, redirect directly to original application portal
        if (($job->source && $job->source->type === 'external') || $job->company?->is_external) {
            if (! empty($job->job_url)) {
                return redirect()->away($job->job_url);
            }
            return back()->with('info', 'This is an external listing without a direct application form.');
        }

        try {
            $this->applicationService->apply($student, $job, $request->only([
                'cover_letter_url', 'expected_salary', 'current_ctc', 'notes'
            ]));

            return redirect('/applications')->with('success', 'Application submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Refresh active external feeds if stale or explicitly requested.
     */
    protected function refreshExternalFeedsIfNeeded(bool $force = false): void
    {
        try {
            $query = JobSource::where('is_active', true)
                ->where('type', 'external');

            if (! $force) {
                // Background sync only if last sync was > 15 minutes ago
                $query->where(function ($q) {
                    $q->whereNull('last_fetched_at')
                      ->orWhere('last_fetched_at', '<', now()->subMinutes(15));
                });
            }

            $sources = $query->limit(3)->get();
            foreach ($sources as $source) {
                try {
                    $this->jobImportService->importFromSource($source, ['limit' => 20]);
                } catch (\Exception $e) {
                    Log::warning("Feed refresh skipped for {$source->name}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::warning("External feeds refresh failed: " . $e->getMessage());
        }
    }
}
