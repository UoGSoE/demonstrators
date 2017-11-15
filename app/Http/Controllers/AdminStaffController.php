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
        $staff = User::findOrFail($request->user_id);
        $staff->removeFromCourse($request->course_id);
        return response()->json([
            'status' => 'OK',
        ]);
    }

    public function courseInfo($staff_id, $course_id)
    {
        $requestsExist = $applicationsExist = false;
        $staff = User::findOrFail($staff_id);
        $course = Course::findOrFail($course_id);
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
}
