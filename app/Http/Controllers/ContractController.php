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
        return view('admin.contracts.edit', [
            'students' => User::students()->has('applications')->with('applications.request.staff', 'applications.request.course')->orderBy('surname')->get(),
            'noApplications' => DemonstratorApplication::all()->isEmpty(),
        ]);
    }

    public function update(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->toggleContract();
        return response()->json([
            'status' => 'OK',
            'has_contract' => $student->fresh()->has_contract,
            'contract_start' => $student->getFormattedDate('contract_start'),
            'contract_end' => $student->getFormattedDate('contract_end'),
            'student_name' => $student->fullName
        ]);
    }

    public function getDates($id)
    {
        $student = User::findOrFail($id);
        return response()->json([
            'status' => 'OK',
            'has_contract' => $student->fresh()->has_contract,
            'contract_start' => $student->getFormattedDate('contract_start'),
            'contract_end' => $student->getFormattedDate('contract_end'),
            'student_name' => $student->fullName
        ]);
    }

    public function updateDates(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->updateContractDates($request->contract_start, $request->contract_end);
        return response()->json([
            'status' => 'OK',
            'contract_start' => $student->fresh()->getFormattedDate('contract_start'),
            'contract_end' => $student->getFormattedDate('contract_end'),
            'id' => $student->id
        ]);
    }

    public function manualWithdraw(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $applications = DemonstratorApplication::findOrFail($request->applications);
        $student->notify(new AdminManualWithdraw($applications, $student->forenames));
        foreach ($applications as $application) {
            $application->delete();
        }
        return redirect()->route('admin.edit_contracts')->with('success_message', "$student->fullName's applications were removed.");
    }

    public function megaDelete(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        foreach ($student->applications as $application) {
            $application->delete();
        }
        $student->has_contract = false;
        $student->returned_rtw = false;
        $student->save();
        return redirect()->route('admin.edit_contracts')->with('success_message', "All of $student->fullName's applications were removed and reset their RTW and contract status.");
    }
}
