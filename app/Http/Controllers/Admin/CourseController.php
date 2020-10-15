<?php

namespace App\Http\Controllers\Admin;

use App\Course;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        return view('admin.courses.index', [
            'courses' => Course::withCount('staff', 'requests')->orderBy('code')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:courses',
            'title' => 'required',
        ]);

        $course = new Course($request->all());
        $course->save();

        return redirect()->route('admin.courses.index')->with('success_message', "Course $course->fullTitle saved.");
    }

    public function edit($id)
    {
        return view('admin.courses.edit', [
            'course' => Course::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => ['required', Rule::unique('courses')->ignore($id)],
            'title' => 'required',
        ]);

        $course = Course::findOrFail($id);
        $course->update($request->all());

        return redirect()->route('admin.courses.index')->with('success_message', "Course $course->fullTitle updated.");
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        if ($course->staff()->count() or $course->requests()->count()) {
            return redirect()->route('admin.courses.edit', $course->id)
                ->withErrors(['in_use' => "Course $course->fullTitle cannot be delete as it is still assigned to staff
                    or has requests active. Please manually remove this course from staff members.",
                ]);
        }
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success_message', "Deleted course: $course->fullTitle");
    }
}
