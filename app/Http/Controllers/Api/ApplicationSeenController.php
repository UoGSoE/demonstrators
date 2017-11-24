<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApplicationSeenController extends Controller
{
    public function update(Request $request)
    {
        // When are admins are viewing this there is no user ID so just bail out
        if (!$request->user_id) {
            return response()->json(['status' => 'OK']);
        }
        $user = User::findOrFail($request->user_id);
        $course = Course::findOrFail($request->course_id);
        $user->markApplicationsSeen($course);
        return response()->json(['status' => 'OK']);
    }
}
