<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use App\DemonstratorRequest;
use Illuminate\Http\Request;
use App\DemonstratorApplication;

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

        return redirect()->route('admin.system.index')
            ->with([
                'success_message' => "Removed students with contracts expired before $request->contract_expiration."
            ]);
    }

    public function resetRequests(Request $request)
    {
        //Remove start date from demonstrator requests and remove applications for it
        DemonstratorRequest::all()->each(function ($demRequest) use ($request) {
            if ($demRequest->start_date < $request->request_start) {
                $demRequest->start_date = null;
                $demRequest->applications->each->delete();
                $demRequest->save();
            }
        });

        return redirect()->route('admin.system.index')
            ->with([
                'success_message' => "Reset requests that started before $request->request_start."
            ]);
    }
}
