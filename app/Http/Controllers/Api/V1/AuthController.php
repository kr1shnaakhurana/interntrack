<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register a new student or company user.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:student,company,admin',
            // Student specific fields
            'roll_no' => 'nullable|string|max:100',
            'program' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:20',
            // Company specific fields
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'sector' => 'nullable|string|in:private,government,ngo,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => $request->password,
            'role' => $request->role,
            'is_active' => true,
        ]);

        if ($user->role === 'student') {
            Student::create([
                'user_id' => $user->id,
                'roll_no' => $request->roll_no,
                'program' => $request->program,
                'department' => $request->department,
                'year' => $request->year,
            ]);
        } elseif ($user->role === 'company') {
            Company::create([
                'user_id' => $user->id,
                'name' => $request->company_name ?? $user->name,
                'website' => $request->website,
                'sector' => $request->sector ?? 'private',
                'is_claimed' => false,
                'is_external' => false,
            ]);
        } elseif ($user->role === 'admin') {
            Admin::create([
                'user_id' => $user->id,
                'name' => $user->name,
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user->load(['student', 'company', 'admin']),
                'token' => $token,
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * Authenticate user and issue access token.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::where('email', strtolower($request->email))->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is suspended or inactive.',
            ], Response::HTTP_FORBIDDEN);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Logged in successfully',
            'data' => [
                'user' => $user->load(['student', 'company', 'admin']),
                'token' => $token,
            ],
        ]);
    }

    /**
     * Revoke current user session token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()->load(['student', 'company', 'admin']),
            ],
        ]);
    }
}
