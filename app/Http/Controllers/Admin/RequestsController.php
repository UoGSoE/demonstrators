<?php

namespace App\Http\Controllers\Admin;

use App\Models\DegreeLevel;
use App\Models\DemonstratorRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RequestsController extends Controller
{
    public function index()
    {
        $staff = User::staff()->with('requests', 'courses.requests.applications.student', 'courses.requests.applications.request')->orderBy('surname')->get();
        $degreeLevels = DegreeLevel::with('requests.applications.student.degreeLevel')->get();
        $noRequests = DemonstratorRequest::count() === 0;

        return view('admin.requests', compact('staff', 'degreeLevels', 'noRequests'));
    }
}
