<?php

namespace App\Http\Controllers\Api\Resource;

use App\Http\Controllers\Controller;
use App\Http\Resources\User as UserResource;
use App\User;

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
