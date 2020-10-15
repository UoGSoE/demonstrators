<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function store($id)
    {
        session(['original_id' => Auth::id()]);
        activity()->log("Started impersonating user $id.");
        Auth::loginUsingId($id);

        return redirect('/');
    }

    public function destroy()
    {
        Auth::loginUsingId(session('original_id'));
        session()->forget('original_id');

        return redirect('/');
    }
}
