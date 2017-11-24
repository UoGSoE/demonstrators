<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function show($staffId, $courseId)
    {
        $requestsExist = false;
        $applicationsExist = false;
        $staff = User::findOrFail($staffId);
        $course = Course::findOrFail($courseId);
        $requests = $staff->requestsForCourse($course);

        if ($requests->count()) {
            $requestsExist = true;
            foreach ($requests as $request) {
                if ($request->applications->count()) {
                    $applicationsExist = true;
                    break;
                }
            }
        }

        return response()->json([
            'status' => 'OK',
            'requests' => $requestsExist,
            'applications' => $applicationsExist,
        ]);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'course_id' => 'required|integer',
        ]);

        User::findOrFail($request->user_id)->removeFromCourse($request->course_id);

        return response()->json([
            'status' => 'OK',
        ]);
    }
}
