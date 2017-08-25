<?php

namespace App\Http\Controllers;

use App\DemonstratorRequest;
use App\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function staff()
    {
        $staff = User::staff()->with('courses.requests.applications')->orderBy('surname')->get();
        $noRequests = DemonstratorRequest::all()->isEmpty();
        return view('admin.staff', compact('staff', 'noRequests'));
    }
}
