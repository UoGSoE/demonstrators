<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\DemonstratorApplication;
use App\Http\Controllers\Controller;

class PositionOfferController extends Controller
{
    public function confirm(DemonstratorApplication $application)
    {
        $application->studentConfirms();
        return response()->json(['status' => 'OK']);
    }

    public function decline(DemonstratorApplication $application)
    {
        $application->studentDeclines();
        return response()->json(['status' => 'OK']);
    }
}
