<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AdminStaffController extends Controller
{
    public function index()
    {
        $staff = User::staff()->orderBy('surname')->get();
        return view('admin.staff.index', compact('staff'));
    }
}
