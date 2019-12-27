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
        activity()->on($application)->log(
            "Demonstrator application " .
            $application->fresh()->isAccepted() ?
            "accepted." :
            "unaccepted."
        );
        return response()->json(['status' => 'OK']);
    }
}
