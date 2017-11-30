<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\DemonstratorApplication;
use App\Notifications\AdminManualWithdraw;
use App\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function edit()
    {
        $students = User::students()
                    ->with('applications.request.staff', 'applications.request.course')
                    ->orderBy('surname')
                    ->get();

        return view('admin.contracts.edit', compact('students'));
    }

    public function destroy(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $applications = DemonstratorApplication::findOrFail($request->applications);

        //$student->notify(new AdminManualWithdraw($applications, $student->forenames));
        $applications->each(function ($application) {
            $application->emaillogs->each->delete();
        });
        $applications->each->delete();

        return redirect()->route('admin.edit_contracts')
            ->with('success_message', "{$student->fullName}'s applications were removed.");
    }
}
