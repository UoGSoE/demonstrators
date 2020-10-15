<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function update($id)
    {
        $staff = User::findOrFail($id);
        $staff->toggleAdmin();

        return response()->json([
            'status' => 'OK',
        ]);
    }
}
