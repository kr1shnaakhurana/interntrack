<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $skills = Skill::query()
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'skills' => $skills,
            ],
        ]);
    }

    public function jobSkills(string $id): JsonResponse
    {
        $job = Job::with('skills')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'skills' => $job->skills,
            ],
        ]);
    }
}
