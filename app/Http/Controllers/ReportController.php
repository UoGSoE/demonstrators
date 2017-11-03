<?php

namespace App\Http\Controllers;

use App\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\DemonstratorRequest;
use App\Queries\FullyConfirmedStudents;
use App\Queries\NeglectedRequestsByCourse;
use App\Queries\FullyConfirmedStudentsWithCourses;

class ReportController extends Controller
{
    public function output1()
    {
        return view('admin.reports.output1', [
            'courses' => Course::with('staff.requests.applications.student')->get()
        ]);
    }

    public function output2()
    {
        return view('admin.reports.output2', [
            'courses' => Course::with('staff.requests.applications.student')->get()
        ]);
    }

    public function output3()
    {
        return view('admin.reports.output3', [
            'students' => (new FullyConfirmedStudents)->get()
        ]);
    }

    public function output4()
    {
        return view('admin.reports.output4', [
            'courses' => (new FullyConfirmedStudentsWithCourses)->get()
        ]);
    }

    public function output5()
    {
        return view('admin.reports.output5', [
            'requests' => DemonstratorRequest::doesntHave('applications')->get()
        ]);
    }

    public function output6()
    {
        return view('admin.reports.output6', [
            'applications' => (new NeglectedRequestsByCourse)->get()
        ]);
    }
}
