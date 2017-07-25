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
        $request->user()->applyFor($demRequest, $request->hours);
        return response()->json(['status' => 'OK']);
    }
}
