<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SavedJob;
use Illuminate\Http\Request;

class WebSavedJobController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;

        $savedJobs = SavedJob::with(['job.company.latestVerification', 'job.source'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('saved-jobs.index', compact('savedJobs'));
    }

    public function destroy(Request $request, string $id)
    {
        $student = $request->user()->student;

        SavedJob::where('student_id', $student->id)
            ->where('id', $id)
            ->delete();

        return back()->with('success', 'Job removed from saved items.');
    }
}
