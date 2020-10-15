<?php

namespace App\Http\Controllers\Api;

use App\Models\DemonstratorApplication;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PositionOfferController extends Controller
{
    public function confirm(DemonstratorApplication $application)
    {
        $application->studentConfirms();
        activity()->on($application)->log('Student accepted job offer.');

        return response()->json(['status' => 'OK']);
    }

    public function decline(DemonstratorApplication $application)
    {
        $application->studentDeclines();
        activity()->on($application)->log('Student declined job offer.');

        return response()->json(['status' => 'OK']);
    }
}
