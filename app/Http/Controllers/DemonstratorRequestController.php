<?php

namespace App\Http\Controllers;

use App\DemonstratorRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DemonstratorRequestController extends Controller
{
    public function update(Request $request)
    {
        $staff = User::findOrFail($request->staff_id);
        $this->validate($request, [
            'course_id' => 'required',
            'hours_needed' => 'required|integer|min:1',
            'demonstrators_needed' => 'required|integer|min:1',
            'type' => 'required',
            'semester_1' => 'required_without_all:semester_2,semester_3',
            'semester_2' => 'required_without_all:semester_1,semester_3',
            'semester_3' => 'required_without_all:semester_1,semester_2',
        ]);
        $demRequest = $staff->requestDemonstrators([
            'course_id' => $request->course_id,
            'type' => $request->type,
            'hours_needed' => $request->hours_needed,
            'demonstrators_needed' => $request->demonstrators_needed,
            'semester_1' => $request->semester_1 ? true : false,
            'semester_2' => $request->semester_2 ? true : false,
            'semester_3' => $request->semester_3 ? true : false,
            'skills' => $request->skills,
        ]);

        return response()->json(['status' => 'OK', 'request' => $demRequest]);
    }

    public function destroy(DemonstratorRequest $demRequest, Request $request)
    {
        $staff = User::findOrFail($demRequest->staff_id);
        if ($demRequest->applications()->accepted()->count()) {
            throw new \Exception("Cannot delete a request when an application has been accepted.");
        }
        $staff->withdrawRequest($demRequest);
        return response()->json(['status' => 'OK']);
    }
}
