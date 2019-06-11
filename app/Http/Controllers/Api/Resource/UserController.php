<?php

namespace App\Http\Controllers\Api\Resource;

use App\User;
use App\Http\Resources\User as UserResource;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::orderBy('surname')->get());
    }

    public function show($id)
    {
        return new UserResource(User::find($id));
    }
}
