<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class ContractDateController extends Controller
{
    public function show($id)
    {
        $student = User::findOrFail($id);

        return response()->json([
            'status' => 'OK',
            'has_contract' => $student->fresh()->has_contract,
            'contract_start' => $student->contract_start,
            'contract_end' => $student->contract_end,
            'student_name' => $student->fullName,
        ]);
    }

    public function update(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->updateContractDates($request->contract_start, $request->contract_end);

        return response()->json([
            'status' => 'OK',
            'contract_start' => $student->fresh()->getFormattedDate('contract_start'),
            'contract_end' => $student->getFormattedDate('contract_end'),
            'id' => $student->id,
        ]);
    }
}
