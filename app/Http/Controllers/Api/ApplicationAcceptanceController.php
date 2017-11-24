<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\DemonstratorApplication;
use App\Http\Controllers\Controller;

class ApplicationAcceptanceController extends Controller
{
    public function update(DemonstratorApplication $application)
    {
        $application->toggleAccepted();
        return response()->json(['status' => 'OK']);
    }
}
