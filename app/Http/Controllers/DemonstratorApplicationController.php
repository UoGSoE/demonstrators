<?php

namespace App\Http\Controllers;

use App\DemonstratorApplication;
use App\DemonstratorRequest;
use Illuminate\Http\Request;

class DemonstratorApplicationController extends Controller
{
    public function toggleAccepted(DemonstratorApplication $application)
    {
        $application->toggleAccepted();
        return response()->json(['status' => 'OK']);
    }

    public function apply(Request $request, DemonstratorRequest $demRequest)
    {
        if ($request->hours == 0) {
            return $this->withdraw($request, $demRequest);
        }
        $request->user()->applyFor($demRequest, $request->hours);
        return response()->json(['status' => 'OK']);
    }

    protected function withdraw($request, $demRequest)
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
