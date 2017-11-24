<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Course;
use App\DemonstratorRequest;
use Illuminate\Http\Request;
use App\DemonstratorApplication;
use App\Http\Controllers\Controller;

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
