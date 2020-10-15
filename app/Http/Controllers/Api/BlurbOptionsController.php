<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class BlurbOptionsController extends Controller
{
    public function update(User $user)
    {
        $user->disableBlurb();

        return response()->json(['status' => 'OK']);
    }
}
