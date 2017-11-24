<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserNotesController extends Controller
{
    public function update(User $user, Request $request)
    {
        $user->update(['notes' => $request->notes]);
        return response()->json(['status' => 'OK']);
    }
}
