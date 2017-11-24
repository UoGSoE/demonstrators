<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BlurbOptionsController extends Controller
{
    public function update(User $user)
    {
        $user->disableBlurb();
        return response()->json(['status' => 'OK']);
    }
}
