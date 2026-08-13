<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Company;
use App\Models\CompanyVerification;
use App\Models\Job;
use App\Models\User;
use App\Services\Companies\CompanyVerificationService;
use App\Services\Jobs\JobAggregatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function __construct(
        protected CompanyVerificationService $verificationService,
        protected JobAggregatorService $jobAggregatorService
    ) {}

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => User::count(),
                'total_students' => User::where('role', 'student')->count(),
                'total_companies' => Company::count(),
                'pending_verifications' => CompanyVerification::where('status', 'pending')->where('is_latest', true)->count(),
                'total_jobs' => Job::count(),
                'active_jobs' => Job::where('is_active', true)->count(),
            ],
        ]);
    }

    public function companyVerifications(Request $request): JsonResponse
    {
        $verifications = CompanyVerification::with(['company', 'reviewer'])
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $verifications,
        ]);
    }

    public function approveVerification(Request $request, string $id): JsonResponse
    {
        $verification = CompanyVerification::findOrFail($id);

        $approved = $this->verificationService->approveVerification(
            $verification,
            $request->user(),
            $request->get('remarks')
        );

        return response()->json([
            'success' => true,
            'message' => 'Company verification approved successfully',
            'data' => [
                'verification' => $approved->load('company'),
            ],
        ]);
    }

    public function rejectVerification(Request $request, string $id): JsonResponse
    {
        $verification = CompanyVerification::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'remarks' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Rejection requires remarks/reason.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rejected = $this->verificationService->rejectVerification(
            $verification,
            $request->user(),
            $request->remarks
        );

        return response()->json([
            'success' => true,
            'message' => 'Company verification rejected',
            'data' => [
                'verification' => $rejected->load('company'),
            ],
        ]);
    }

    public function jobs(Request $request): JsonResponse
    {
        $jobs = Job::with(['company', 'source'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $jobs,
        ]);
    }

    public function companies(Request $request): JsonResponse
    {
        $companies = Company::with('latestVerification')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $companies,
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $users = User::with(['student', 'company', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    public function logs(Request $request): JsonResponse
    {
        $logs = AdminLog::with('admin.user')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 30));

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function triggerExternalIngestion(Request $request): JsonResponse
    {
        $source = $request->get('source', 'sample_external_feed');
        $metrics = $this->jobAggregatorService->ingestFromSource($source);

        return response()->json([
            'success' => true,
            'message' => 'External job ingestion completed successfully.',
            'data' => [
                'metrics' => $metrics,
            ],
        ]);
    }
}
