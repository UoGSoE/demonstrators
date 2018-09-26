<?php

namespace App\Http\Controllers\Api;

use App\User;
use Carbon\Carbon;
use App\DemonstratorRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestController extends Controller
{
    public function update(Request $request)
    {
        $staff = User::findOrFail($request->staff_id);
        $this->validate($request, [
            'course_id' => 'required',
            'start_date' => 'required',
            'hours_needed' => 'required|integer|min:1',
            'hours_training' => 'nullable|integer',
            'demonstrators_needed' => 'required|integer|min:1',
            'type' => 'required',
            'semester_1' => 'required_without_all:semester_2,semester_3',
            'semester_2' => 'required_without_all:semester_1,semester_3',
            'semester_3' => 'required_without_all:semester_1,semester_2',
        ]);
        $demRequest = $staff->requestDemonstrators([
            'course_id' => $request->course_id,
            'type' => $request->type,
            'start_date' => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d'),
            'hours_needed' => $request->hours_needed,
            'hours_training' => $request->hours_training,
            'demonstrators_needed' => $request->demonstrators_needed,
            'semester_1' => $request->semester_1 ? true : false,
            'semester_2' => $request->semester_2 ? true : false,
            'semester_3' => $request->semester_3 ? true : false,
            'skills' => $request->skills,
            'degree_levels' => $request->degree_levels
        ]);
        if (!$demRequest) {
            return response()->json(['message' => 'Cannot change hours of a request when an application has been accepted.'], 500);
        }
        $demRequest->start_date = $demRequest->getFormattedStartDate();
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


    public function checkForEmptyDates($staff_id)
    {
        $staff = User::findOrFail($staff_id);
        return response()->json([
            'status' => 'OK',
            'result' => $staff->hasEmptyDates()
        ]);
    }
}
