<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateNotes(User $user, Request $request)
    {
        $user->setNotes($request->notes);
        return response()->json(['status' => 'OK']);
    }

    public function disableBlurb(User $user)
    {
        $user->disableBlurb();
        return response()->json(['status' => 'OK']);
    }
}
