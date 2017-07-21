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
            'starting' => 'required|date_format:d/m/Y',
            'ending' => 'required|date_format:d/m/Y',
        ]);
        auth()->user()->requestDemonstrators([
            'course_id' => $request->course_id,
            'hours_needed' => $request->hours_needed,
            'demonstrators_needed' => $request->demonstrators_needed,
            'starting' => Carbon::createFromFormat('d/m/Y', $request->starting),
            'ending' => Carbon::createFromFormat('d/m/Y', $request->ending),
            'skills' => $request->skills,
        ]);

        return response()->json(['status' => 'OK']);
    }
}
