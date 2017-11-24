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
        $user = User::findOrFail($request->user_id);
        $course = Course::findOrFail($request->course_id);
        $user->markApplicationsSeen($course);
        return response()->json(['status' => 'OK']);
    }
}
