<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        User::create($request->all() + ['is_student' => true]);

        return redirect()->route('admin.edit_contracts')->withSuccess('Saved');
    }

    public function destroy(Request $request)
    {
        $student = User::findOrFail($request->student_id);

        $student->emaillogs->each->delete();
        $student->applications->each->delete();
        $student->delete();

        return redirect()->route('admin.edit_contracts')->with(
            'success_message',
            "All of {$student->fullName}'s applications were removed and they were removed from the system."
        );
    }
}
