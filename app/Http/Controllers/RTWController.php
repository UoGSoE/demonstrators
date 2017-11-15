<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class RTWController extends Controller
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
            'student_name' => $student->fullName
        ]);
    }

    public function getDates($id)
    {
        $student = User::findOrFail($id);
        return response()->json([
            'status' => 'OK',
            'returned_rtw' => $student->fresh()->returned_rtw,
            'rtw_start' => $student->rtw_start,
            'rtw_end' => $student->rtw_end,
            'student_name' => $student->fullName
        ]);
    }

    public function updateDates(Request $request)
    {
        $student = User::findOrFail($request->student_id);
        $student->updateRTWDates($request->rtw_start, $request->rtw_end);
        return response()->json([
            'status' => 'OK',
            'rtw_start' => $student->fresh()->getFormattedDate('rtw_start'),
            'rtw_end' => $student->getFormattedDate('rtw_end'),
            'id' => $student->id
        ]);
    }
}
