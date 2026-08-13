<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    protected function getStudent(Request $request): Student
    {
        $student = $request->user()->student;

        if (! $student) {
            $student = Student::create(['user_id' => $request->user()->id]);
        }

        return $student;
    }

    public function showMe(Request $request): JsonResponse
    {
        $student = $this->getStudent($request);

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student->load(['user', 'skills', 'resumes']),
            ],
        ]);
    }

    public function updateMe(Request $request): JsonResponse
    {
        $student = $this->getStudent($request);

        $validator = Validator::make($request->all(), [
            'roll_no' => 'nullable|string|max:100|unique:students,roll_no,' . $student->id,
            'program' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'phone' => 'nullable|string|max:30',
            'profile_image_url' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $student->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Student profile updated successfully',
            'data' => [
                'student' => $student->load(['user', 'skills']),
            ],
        ]);
    }

    public function myApplications(Request $request): JsonResponse
    {
        $student = $this->getStudent($request);

        $applications = $student->applications()
            ->with(['job.company', 'interviews', 'statusHistory'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }

    public function mySavedJobs(Request $request): JsonResponse
    {
        $student = $this->getStudent($request);

        $saved = $student->savedJobs()
            ->with(['job.company'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $saved,
        ]);
    }

    public function mySkills(Request $request): JsonResponse
    {
        $student = $this->getStudent($request);

        return response()->json([
            'success' => true,
            'data' => [
                'skills' => $student->skills,
            ],
        ]);
    }

    public function addSkill(Request $request): JsonResponse
    {
        $student = $this->getStudent($request);

        $validator = Validator::make($request->all(), [
            'skill_id' => 'required_without:skill_name|uuid|exists:skills,id',
            'skill_name' => 'required_without:skill_id|string|max:100',
            'proficiency' => 'nullable|string|in:beginner,intermediate,advanced',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $skillId = $request->skill_id;
        if (! $skillId && $request->skill_name) {
            $skill = Skill::firstOrCreate(
                ['name' => trim($request->skill_name)],
                ['category' => 'General']
            );
            $skillId = $skill->id;
        }

        $student->skills()->syncWithoutDetaching([
            $skillId => ['proficiency' => $request->proficiency ?? 'intermediate'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Skill attached to student profile successfully',
            'data' => [
                'skills' => $student->fresh()->skills,
            ],
        ]);
    }
}
