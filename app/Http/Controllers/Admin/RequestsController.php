<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\DemonstratorRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestsController extends Controller
{
    public function index()
    {
        $staff = User::staff()->with('courses.requests.applications')->orderBy('surname')->get();
        $noRequests = DemonstratorRequest::all()->isEmpty();
        return view('admin.requests', compact('staff', 'noRequests'));
    }
}
