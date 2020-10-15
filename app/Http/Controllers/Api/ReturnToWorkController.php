<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class ReturnToWorkController extends Controller
{
    public function update(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->toggleRTW();

        return response()->json([
            'status' => 'OK',
            'returned_rtw' => $student->fresh()->returned_rtw,
            'rtw_start' => $student->rtw_start,
            'rtw_end' => $student->rtw_end,
            'student_name' => $student->fullName,
        ]);
    }
}
