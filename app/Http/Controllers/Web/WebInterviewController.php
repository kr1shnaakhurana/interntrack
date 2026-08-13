<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use Illuminate\Http\Request;

class WebInterviewController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;

        $interviews = Interview::with(['application.job.company'])
            ->whereHas('application', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->orderBy('scheduled_at', 'asc')
            ->paginate(15);

        return view('interviews.index', compact('interviews'));
    }
}
