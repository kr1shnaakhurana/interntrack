<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        if (! $student) {
            $student = Student::create(['user_id' => $user->id]);
        }

        $student->load(['skills', 'resumes']);
        $allSkills = Skill::orderBy('name', 'asc')->get();

        return view('profile.index', compact('user', 'student', 'allSkills'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $student = $user->student;

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'program' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:20',
            'roll_no' => 'nullable|string|max:100',
        ]);

        $user->update(['name' => $request->name]);
        $student->update($request->only(['phone', 'program', 'department', 'year', 'roll_no']));

        return back()->with('success', 'Profile updated successfully.');
    }

    public function uploadResume(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'file_url' => 'nullable|url|max:500',
        ]);

        $student = $request->user()->student;

        $fileUrl = $request->file_url;
        if ($request->hasFile('resume_file')) {
            $path = $request->file('resume_file')->store('resumes', 'public');
            $fileUrl = '/storage/' . $path;
        }

        if (! $fileUrl) {
            return back()->with('error', 'Please provide a file URL or upload a resume file.');
        }

        $latestVersion = ($student->resumes()->max('version') ?? 0) + 1;

        // If marked default, unmark others
        if ($request->boolean('is_default') || $latestVersion === 1) {
            $student->resumes()->update(['is_default' => false]);
        }

        Resume::create([
            'student_id' => $student->id,
            'title' => $request->title,
            'file_url' => $fileUrl,
            'version' => $latestVersion,
            'is_default' => $request->boolean('is_default') || $latestVersion === 1,
        ]);

        return back()->with('success', "Resume v{$latestVersion} uploaded successfully!");
    }
}
