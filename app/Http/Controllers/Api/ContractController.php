<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function update(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->toggleContract();

        return response()->json([
            'status' => 'OK',
            'has_contract' => $student->fresh()->has_contract,
            'contract_start' => $student->contract_start,
            'contract_end' => $student->contract_end,
            'student_name' => $student->fullName,
        ]);
    }
}
