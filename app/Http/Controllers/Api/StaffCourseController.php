<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffCourseController extends Controller
{
    public function update(Request $request)
    {
        $staff = User::findOrFail($request->staff_id);
        $reassignUser = User::findOrFail($request->reassign_id);
        $course = Course::findOrFail($request->course_id);

        if ($reassignUser->courses()->where('courses.id', $course->id)->count() > 0) {
            return response()->json([
                'status' => 'Cannot allocate to person on the same course.',
            ], 422);
        }

        $staff->requestsForCourse($course)->each->reassignTo($reassignUser);

        $reassignUser->addToCourse($course->id);
        $staff->removeFromCourse($course->id);

        return response()->json([
            'status' => 'OK',
        ]);
    }

    public function destroy(Request $request)
    {
        $staff = User::findOrFail($request->staff_id);
        $course = Course::findOrFail($request->course_id);
        $staff->requestsForCourse($course)->each(function ($request) use ($staff) {
            $staff->withdrawRequest($request);
        });
        $staff->removeFromCourse($course->id);
        return response()->json([
            'status' => 'OK',
        ]);
    }
}
