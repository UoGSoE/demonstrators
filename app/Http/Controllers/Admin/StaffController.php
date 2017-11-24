<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::staff()->with('courses')->with('requests.applications')->orderBy('surname')->get();
        $courses = Course::all();
        return view('admin.staff.index', compact('staff', 'courses'));
    }

    public function update(Request $request)
    {
        $staff = User::findOrFail($request->staff_id);
        $staff->addToCourse($request->course_id);
        return response()->json([
            'status' => 'OK',
        ]);
    }
}
