<?php

namespace App\Http\Controllers;

use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function index()
    {
        return view('admin.system.index');
    }

    public function expiredContracts(Request $request)
    {
        //Delete students with expired contracts
        User::students()->hasContract()->where(
            'contract_end',
            '<',
            $request->contract_expiration
        )->get()->each(function ($user) {
            $user->emaillogs->each->delete();
            $user->applications->each->delete();
            $user->delete();
        });

        $date = date('d/m/Y', strtotime($request->contract_expiration));

        activity()->log("Removed students with contracts expired before $date.");

        return redirect()->route('admin.system.index')
            ->with([
                'success_message' => "Removed students with contracts expired before $date.",
            ]);
    }

    public function resetRequests(Request $request)
    {
        //Remove start date from demonstrator requests and remove applications for it
        DemonstratorRequest::all()->each(function ($demRequest) use ($request) {
            if ($demRequest->start_date < $request->request_start) {
                $demRequest->start_date = null;
                $demRequest->applications->each(function ($application) {
                    $application->emaillogs->each->delete();
                });
                $demRequest->applications->each->delete();
                $demRequest->save();
            }
        });

        $date = date('d/m/Y', strtotime($request->contract_expiration));

        activity()->log("Reset requests that started before $date.");

        return redirect()->route('admin.system.index')
            ->with([
                'success_message' => "Reset requests that started before $date.",
            ]);
    }
}
