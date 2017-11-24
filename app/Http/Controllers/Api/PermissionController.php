<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function update($id)
    {
        $staff = User::findOrFail($id);
        $staff->toggleAdmin();
        return response()->json([
            'status' => 'OK'
        ]);
    }
}
