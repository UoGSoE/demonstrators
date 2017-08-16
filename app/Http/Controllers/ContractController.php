<?php

namespace App\Http\Controllers;

use App\DemonstratorApplication;
use App\Notifications\AdminManualWithdraw;
use App\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function edit()
    {
        return view('admin.contracts.edit', [
            'students' => User::students()->has('applications')->orderBy('surname')->get()
        ]);
    }

    public function update(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->toggleContract();
        return response()->json(['status' => 'OK']);
    }

    public function updateRTW(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->toggleRTW();
        return response()->json(['status' => 'OK']);
    }

    public function manualWithdraw(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $applications = DemonstratorApplication::findOrFail($request->applications);
        $student->notify(new AdminManualWithdraw($applications));
        foreach ($applications as $application) {
            $application->delete();
        }
        return redirect()->route('admin.edit_contracts')->with('success_message', "{{ $student->fullName }}'s requests were removed");
    }
}
