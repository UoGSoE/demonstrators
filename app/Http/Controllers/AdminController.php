<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function staff()
    {
        $staff = User::staff()->with('courses')->orderBy('surname')->get();
        return view('admin.staff', compact('staff'));
    }
}
