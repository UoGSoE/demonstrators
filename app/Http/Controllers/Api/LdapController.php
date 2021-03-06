<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Auth\Ldap;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LdapController extends Controller
{
    public function show($username = null)
    {
        $user = Ldap::lookUp($username);
        if (!$user) {
            return response()->json(['message' => 'Invalid GUID.'], 404);
        }
        if (User::where('username', $username)->first()) {
            return response()->json(['message' => 'Duplicate username.'], 422);
        }
        if (User::where('email', $user['email'])->first()) {
            return response()->json(['message' => "Duplicate email address (" . $user['email'] . ")."], 422);
        }
        return response()->json($user);
    }
}
