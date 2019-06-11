<?php

namespace App\Http\Controllers\Api\Resource;

use App\User;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Staff as StaffResource;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::staff()->orderBy('surname')->get());
    }

    public function show($id)
    {
        return new StaffResource(User::find($id));
    }
}
