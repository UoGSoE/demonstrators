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
        $courses = Course::all()->sortBy('code')->values();
        return view('admin.staff.index', compact('staff', 'courses'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        User::create($request->all() + ['is_student' => false]);
        return redirect()->route('admin.staff.index')->withSuccess('Saved');
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
