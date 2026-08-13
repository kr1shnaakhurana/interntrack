<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\Companies\CompanyVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyVerificationService $verificationService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'logo_url' => 'nullable|url|max:500',
            'sector' => 'nullable|string|in:private,government,ngo,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $request->user();

        $company = Company::create([
            'user_id' => $user ? $user->id : null,
            'name' => $request->name,
            'website' => $request->website,
            'industry' => $request->industry,
            'size' => $request->size,
            'location' => $request->location,
            'about' => $request->about,
            'logo_url' => $request->logo_url,
            'sector' => $request->sector ?? 'private',
            'is_claimed' => $user ? true : false,
            'is_external' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully',
            'data' => [
                'company' => $company->load('latestVerification'),
            ],
        ], Response::HTTP_CREATED);
    }

    public function show(string $id): JsonResponse
    {
        $company = Company::with(['latestVerification', 'jobs' => function ($q) {
            $q->where('is_active', true)->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'company' => $company,
            ],
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $company = Company::findOrFail($id);

        // Authorization check
        if ($request->user() && ! $request->user()->isAdmin() && $company->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to edit this company profile.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'website' => 'nullable|url|max:255',
            'industry' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'about' => 'nullable|string',
            'logo_url' => 'nullable|url|max:500',
            'sector' => 'nullable|string|in:private,government,ngo,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $company->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully',
            'data' => [
                'company' => $company->fresh()->load('latestVerification'),
            ],
        ]);
    }

    public function requestVerification(Request $request, string $id): JsonResponse
    {
        $company = Company::findOrFail($id);

        if ($request->user() && ! $request->user()->isAdmin() && $company->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validator = Validator::make($request->all(), [
            'documents_url' => 'required|url|max:500',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $verification = $this->verificationService->requestVerification(
            $company,
            $request->documents_url,
            $request->remarks
        );

        return response()->json([
            'success' => true,
            'message' => 'Company verification request submitted successfully.',
            'data' => [
                'verification' => $verification,
            ],
        ], Response::HTTP_CREATED);
    }

    public function getVerifications(string $id): JsonResponse
    {
        $company = Company::findOrFail($id);

        $verifications = $company->verifications()
            ->with('reviewer')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'verifications' => $verifications,
            ],
        ]);
    }
}
