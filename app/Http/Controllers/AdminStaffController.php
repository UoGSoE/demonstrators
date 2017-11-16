<?php

namespace App\Http\Controllers;

use App\User;
use App\Course;
use Illuminate\Http\Request;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staff = User::staff()->with('courses')->with('requests.applications')->orderBy('surname')->get();
        $courses = Course::all();
        return view('admin.staff.index', compact('staff', 'courses'));
    }

    public function update(Request $request)
    {
        $staff = User::findOrFail($request->user_id);
        $staff->addToCourse($request->course_id);
        return response()->json([
            'status' => 'OK',
        ]);
    }

    public function removeCourse(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'course_id' => 'required|integer',
        ]);
        $staff = User::findOrFail($request->user_id);
        $staff->removeFromCourse($request->course_id);
        return response()->json([
            'status' => 'OK',
        ]);
    }

    public function courseInfo($staffId, $courseId)
    {
        $requestsExist = $applicationsExist = false;
        $staff = User::findOrFail($staffId);
        $course = Course::findOrFail($courseId);
        $requests = $staff->requestsForCourse($course);
        if ($requests->count()) {
            $requestsExist = true;
            foreach ($requests as $request) {
                if ($request->applications->count()) {
                    $applicationsExist = true;
                }
            }
        }
        return response()->json([
            'status' => 'OK',
            'requests' => $requestsExist,
            'applications' => $applicationsExist,
        ]);
    }

    public function removeRequests(Request $request)
    {
        $staff = User::findOrFail($request->staff_id);
        $course = Course::findOrFail($request->course_id);
        $staff->requestsForCourse($course)->each(function ($request) use ($staff) {
            $staff->withdrawRequest($request);
        });
        return response()->json([
            'status' => 'OK',
        ]);
    }
}
