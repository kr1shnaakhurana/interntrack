<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    /**
     * Search, filter, and paginate public jobs.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Job::with(['company.latestVerification', 'source', 'skills'])
            ->where('is_active', true);

        // Filter: Keyword (title, description, skills)
        if ($request->filled('keyword')) {
            $keyword = '%' . $request->keyword . '%';
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', $keyword)
                  ->orWhere('description', 'like', $keyword)
                  ->orWhere('skills_required', 'like', $keyword);
            });
        }

        // Filter: Job type (internship, full-time, part-time, research, fellowship, government, other)
        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        // Filter: Location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter: Remote
        if ($request->has('is_remote')) {
            $query->where('is_remote', filter_var($request->is_remote, FILTER_VALIDATE_BOOLEAN));
        }

        // Filter: Company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter: Sector (private, government, ngo, other)
        if ($request->filled('sector')) {
            $sector = $request->sector;
            $query->whereHas('company', function ($q) use ($sector) {
                $q->where('sector', $sector);
            });
        }

        // Filter: Verified Company only
        if ($request->boolean('verified_only')) {
            $query->whereHas('company.verifications', function ($q) {
                $q->where('is_latest', true)->where('status', 'approved');
            });
        }

        // Filter: Source
        if ($request->filled('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        // Filter: Skills
        if ($request->filled('skill')) {
            $skillName = $request->skill;
            $query->whereHas('skills', function ($q) use ($skillName) {
                $q->where('name', 'like', '%' . $skillName . '%');
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'posted_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $allowedSorts = ['posted_at', 'created_at', 'deadline', 'title'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, strtolower($sortDir) === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('posted_at', 'desc');
        }

        $jobs = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
    }

    /**
     * Get single job details.
     */
    public function show(string $id): JsonResponse
    {
        $job = Job::with(['company.latestVerification', 'source', 'skills', 'jobSkills.skill'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'job' => $job,
            ],
        ]);
    }

    /**
     * Direct company job posting endpoint.
     */
    public function storeDirectJob(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user ? $user->company : null;

        if (! $company) {
            return response()->json([
                'success' => false,
                'message' => 'Company profile required to publish direct jobs.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'job_type' => 'required|string|in:internship,full-time,part-time,research,fellowship,government,other',
            'location' => 'nullable|string|max:255',
            'is_remote' => 'boolean',
            'stipend_salary' => 'nullable|string|max:100',
            'deadline' => 'nullable|date',
            'experience_required' => 'nullable|string|max:100',
            'skills_required' => 'nullable|string',
            'job_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $directSource = JobSource::firstOrCreate(
            ['name' => 'InternTrack Direct'],
            ['type' => 'direct', 'is_active' => true]
        );

        $job = Job::create([
            'company_id' => $company->id,
            'source_id' => $directSource->id,
            'title' => $request->title,
            'description' => $request->description,
            'job_type' => $request->job_type,
            'location' => $request->location,
            'is_remote' => $request->boolean('is_remote'),
            'stipend_salary' => $request->stipend_salary,
            'deadline' => $request->deadline,
            'experience_required' => $request->experience_required,
            'skills_required' => $request->skills_required,
            'job_url' => $request->job_url,
            'is_active' => true,
            'posted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job posted successfully',
            'data' => [
                'job' => $job->load(['company', 'source']),
            ],
        ], Response::HTTP_CREATED);
    }

    public function companyJobs(Request $request): JsonResponse
    {
        $company = $request->user()->company;

        if (! $company) {
            return response()->json([
                'success' => false,
                'message' => 'Company profile required.',
            ], Response::HTTP_FORBIDDEN);
        }

        $jobs = Job::with(['applications', 'skills'])
            ->where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
    }

    public function updateDirectJob(Request $request, string $id): JsonResponse
    {
        $job = Job::findOrFail($id);
        $user = $request->user();

        if (! $user->isAdmin() && ($user->company->id ?? null) !== $job->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to edit this job posting.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'job_type' => 'sometimes|required|string|in:internship,full-time,part-time,research,fellowship,government,other',
            'location' => 'nullable|string|max:255',
            'is_remote' => 'boolean',
            'is_active' => 'boolean',
            'stipend_salary' => 'nullable|string|max:100',
            'deadline' => 'nullable|date',
            'experience_required' => 'nullable|string|max:100',
            'skills_required' => 'nullable|string',
            'job_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $job->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Job posting updated successfully',
            'data' => [
                'job' => $job->fresh()->load(['company', 'source']),
            ],
        ]);
    }

    public function destroyDirectJob(Request $request, string $id): JsonResponse
    {
        $job = Job::findOrFail($id);
        $user = $request->user();

        if (! $user->isAdmin() && ($user->company->id ?? null) !== $job->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], Response::HTTP_FORBIDDEN);
        }

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job posting deleted successfully',
        ]);
    }
}
