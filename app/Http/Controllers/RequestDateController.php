<?php

namespace App\Http\Controllers;

use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RequestDateController extends Controller
{
    public function update()
    {
        if (DemonstratorApplication::count() > 0) {
            return redirect()->back()->withErrors([
                'applications' => 'Cannot update years when there are still applications for requests.',
            ]);
        }
        DemonstratorRequest::all()->each->updateYear();

        activity()->log('Updated year for all demonstrator requests');

        return redirect()->back();
    }
}
