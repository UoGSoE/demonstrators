<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function edit()
    {
        return view('admin.contracts.edit', [
            'students' => User::students()->orderBy('surname')->get()
        ]);
    }

    public function update(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->toggleContract();
        return response()->json(['status' => 'OK']);
    }
}
