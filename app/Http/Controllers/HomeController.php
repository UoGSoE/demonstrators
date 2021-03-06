<?php

namespace App\Http\Controllers;

use App\Course;
use App\DegreeLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Auth::user()->is_student) {
            return view('student.home', [
                'courses' => Course::with('staff', 'requests.degreeLevels', 'requests.applications', 'requests.acceptedApplications', 'requests.staff.requests')->orderBy('code')->get(),
                // 'courses' => Course::with('requests.applications', 'requests.staff')->orderBy('code')->get(),
                'degreeLevels' => DegreeLevel::all(),
            ]);
        }
        return view('staff.home', [
            'degreeLevels' => DegreeLevel::with('requests.applications.student.degreeLevel')->get(),
            // 'degreeLevels' => DegreeLevel::all(),
            'courses' => request()->user()->courses()->with(['requests.applications.student', 'requests.applications.request'])->get(),
        ]);
    }
}
