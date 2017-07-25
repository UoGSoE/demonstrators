<?php

namespace App\Http\Controllers;

use App\DemonstratorApplication;
use Illuminate\Http\Request;

class DemonstratorApplicationController extends Controller
{
    public function toggleAccepted(DemonstratorApplication $application)
    {
        $application->toggleAccepted();
        return response()->json(['status' => 'OK']);
    }
}
