<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Interview;
use App\Services\Interviews\InterviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class InterviewController extends Controller
{
    public function __construct(
        protected InterviewService $interviewService
    ) {}

    public function store(Request $request, string $applicationId): JsonResponse
    {
        $application = Application::findOrFail($applicationId);

        $validator = Validator::make($request->all(), [
            'round_number' => 'nullable|integer|min:1',
            'round_name' => 'nullable|string|max:100',
            'interview_type' => 'nullable|string|in:phone,online,offline,technical,hr',
            'scheduled_at' => 'nullable|date',
            'interviewer' => 'nullable|string|max:255',
            'location_meeting_link' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $interview = $this->interviewService->scheduleRound($application, $request->all(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Interview round scheduled successfully',
            'data' => [
                'interview' => $interview,
            ],
        ], Response::HTTP_CREATED);
    }

    public function index(string $applicationId): JsonResponse
    {
        $application = Application::findOrFail($applicationId);

        return response()->json([
            'success' => true,
            'data' => [
                'interviews' => $application->interviews,
            ],
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $interview = Interview::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'result' => 'required|string|in:pending,cleared,rejected',
            'is_final_round' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $updatedInterview = $this->interviewService->updateResult(
            $interview,
            $request->result,
            $request->user(),
            $request->boolean('is_final_round'),
            $request->notes
        );

        return response()->json([
            'success' => true,
            'message' => 'Interview round result updated successfully',
            'data' => [
                'interview' => $updatedInterview->fresh()->load('application'),
            ],
        ]);
    }
}
