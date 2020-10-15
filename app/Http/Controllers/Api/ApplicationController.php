<?php

namespace App\Http\Controllers\Api;

use App\Course;
use App\DemonstratorApplication;
use App\DemonstratorRequest;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request, DemonstratorRequest $demRequest)
    {
        $request->user()->applyFor($demRequest);

        return response()->json(['status' => 'OK']);
    }

    public function destroy(Request $request, DemonstratorRequest $demRequest)
    {
        $application = $demRequest->applications()->where('student_id', $request->user()->id)->firstOrFail();
        try {
            $request->user()->withdraw($application);

            return response()->json(['status' => 'OK']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'ERROR'], 500);
        }
    }
}
