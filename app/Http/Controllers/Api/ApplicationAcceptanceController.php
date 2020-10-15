<?php

namespace App\Http\Controllers\Api;

use App\Models\DemonstratorApplication;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplicationAcceptanceController extends Controller
{
    public function update(DemonstratorApplication $application)
    {
        $application->toggleAccepted();
        activity()->on($application)->log(
            'Demonstrator application '.
            $application->fresh()->isAccepted() ?
            'accepted.' :
            'unaccepted.'
        );

        return response()->json(['status' => 'OK']);
    }
}
