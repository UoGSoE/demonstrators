<?php

namespace App\Http\Controllers;

use App\User;
use App\Course;
use App\DemonstratorRequest;
use Illuminate\Http\Request;
use App\DemonstratorApplication;

class DemonstratorApplicationController extends Controller
{
    public function toggleAccepted(DemonstratorApplication $application)
    {
        $application->toggleAccepted();
        return response()->json(['status' => 'OK']);
    }

    public function store(Request $request, DemonstratorRequest $demRequest)
    {
        $request->user()->applyFor($demRequest);
        return response()->json(['status' => 'OK']);
    }

    public function destroy(Request $request, DemonstratorRequest $demRequest)
    {
        $application = $demRequest->applications()->where('student_id', $request->user()->id)->firstOrFail();
        try {
            $request->user()->withdraw($application);
            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'ERROR'], 500);
        }
    }

    public function studentConfirms(DemonstratorApplication $application)
    {
        $application->studentConfirms();
        return response()->json(['status' => 'OK']);
    }

    public function studentDeclines(DemonstratorApplication $application)
    {
        $application->studentDeclines();
        return response()->json(['status' => 'OK']);
    }

    public function markSeen(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $course = Course::findOrFail($request->course_id);
        $user->markApplicationsSeen($course);
        return response()->json(['status' => 'OK']);
    }
}
