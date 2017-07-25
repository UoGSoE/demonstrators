<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class DemonstratorRequestController extends Controller
{
    public function update(Request $request)
    {
        $this->validate($request, [
            'course_id' => 'required',
            'hours_needed' => 'required|integer|min:1',
            'demonstrators_needed' => 'required|integer|min:1',
            'semester_1' => 'required|boolean',
            'semester_2' => 'required|boolean',
            'semester_3' => 'required|boolean',
        ]);
        auth()->user()->requestDemonstrators([
            'course_id' => $request->course_id,
            'hours_needed' => $request->hours_needed,
            'demonstrators_needed' => $request->demonstrators_needed,
            'semester_1' => $request->semester_1,
            'semester_2' => $request->semester_2,
            'semester_3' => $request->semester_3,
            'skills' => $request->skills,
        ]);

        return response()->json(['status' => 'OK']);
    }
}
