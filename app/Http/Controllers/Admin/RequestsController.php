<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\DemonstratorRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DegreeLevel;

class RequestsController extends Controller
{
    public function index()
    {
        $staff = User::staff()->with('courses.requests.applications')->orderBy('surname')->get();
        $degreeLevels = DegreeLevel::all();
        $noRequests = DemonstratorRequest::all()->isEmpty();
        return view('admin.requests', compact('staff', 'degreeLevels', 'noRequests'));
    }
}
