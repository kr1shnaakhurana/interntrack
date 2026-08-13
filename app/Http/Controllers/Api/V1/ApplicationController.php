<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Services\Applications\ApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends Controller
{
    public function __construct(
        protected ApplicationService $applicationService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile required to submit job applications.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'job_id' => 'required|uuid|exists:jobs,id',
            'cover_letter_url' => 'nullable|url|max:500',
            'expected_salary' => 'nullable|string|max:100',
            'current_ctc' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $job = Job::findOrFail($request->job_id);

        try {
            $application = $this->applicationService->apply($student, $job, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'data' => [
                    'application' => $application->load(['job.company', 'student.user']),
                ],
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Application::with(['job.company', 'student.user', 'interviews', 'statusHistory']);

        if ($user->isStudent() && $user->student) {
            $query->where('student_id', $user->student->id);
        } elseif ($user->isCompany() && $user->company) {
            $query->whereHas('job', function ($q) use ($user) {
                $q->where('company_id', $user->company->id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $application = Application::with(['job.company', 'student.user', 'interviews', 'statusHistory.changedByUser'])
            ->findOrFail($id);

        $user = $request->user();
        if ($user->isStudent() && ($user->student->id ?? null) !== $application->student_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'application' => $application,
            ],
        ]);
    }

    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $application = Application::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:saved,applied,shortlisted,interview,selected,rejected',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $updatedApplication = $this->applicationService->updateStatus(
            $application,
            $request->status,
            $request->user(),
            $request->remarks
        );

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully',
            'data' => [
                'application' => $updatedApplication->load(['statusHistory.changedByUser', 'interviews']),
            ],
        ]);
    }
}
