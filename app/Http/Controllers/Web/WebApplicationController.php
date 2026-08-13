<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class WebApplicationController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;
        if (! $student) {
            $student = \App\Models\Student::create(['user_id' => $request->user()->id]);
        }

        $applications = Application::with(['job.company.latestVerification', 'interviews', 'statusHistory'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('applications.index', compact('applications'));
    }

    public function show(Request $request, string $id)
    {
        $student = $request->user()->student;

        $application = Application::with([
            'job.company.latestVerification',
            'job.source',
            'interviews',
            'statusHistory.changedByUser',
            'student.resumes'
        ])->where('student_id', $student->id)
          ->findOrFail($id);

        return view('applications.show', compact('application'));
    }
}
