<?php

namespace App\Http\Controllers;

use App\User;
use App\Auth\Ldap;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
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
        $name = $student->fullName;
        foreach ($student->applications as $application) {
            $application->delete();
        }
        $student->delete();
        return redirect()->route('admin.edit_contracts')
            ->with(
                'success_message',
                "All of $student->fullName's applications were removed and they were removed from the system."
            );
    }

    public function ldapLookup($username = null)
    {
        $user = Ldap::lookUp($username);
        if (!$user) {
            return response()->json(['message' => 'Invalid GUID.'], 404);
        }
        if (User::where('username', $username)->first()) {
            return response()->json(['message' => 'Duplicate username.'], 422);
        }
        if (User::where('email', $user['email'])->first()) {
            return response()->json(['message' => "Duplicate email address (" . $user['email'] . ")."], 422);
        }
        return response()->json($user);
    }
}
