<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentProfileController extends Controller
{
    public function update(Request $request, User $user)
    {
        $user->update([
            'notes' => $request->notes,
            'degree_level_id' => $request->degree_level_id
        ]);
        return response()->json(['status' => 'OK']);
    }
}
