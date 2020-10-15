<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Queries\AcceptedStudentsWithCourses;
use App\Queries\ConfirmedStudents;
use App\Queries\NeglectedRequestsByCourse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function output1()
    {
        return view('admin.reports.output1', [
            'courses' => Course::with('requests', 'requests.applications.student', 'requests.acceptedApplications', 'staff.requests.applications.student.applications', 'staff.requests.acceptedApplications')->get()->sortBy('title'),
        ]);
    }

    public function output2()
    {
        return view('admin.reports.output2', [
            'courses' => Course::with('requests.applications.student', 'staff.requests.applications.student')->orderBy('title')->get(),
        ]);
    }

    public function output3()
    {
        return view('admin.reports.output3', [
            'students' => (new ConfirmedStudents)->get()->sortBy('student.surname'),
        ]);
    }

    public function output4()
    {
        return view('admin.reports.output4', [
            'courses' => (new AcceptedStudentsWithCourses)->get()->sortBy('title'),
        ]);
    }

    public function output5()
    {
        return view('admin.reports.output5', [
            'requests' => DemonstratorRequest::doesntHave('applications')->with('course', 'staff')->get()->sortBy('course.title'),
        ]);
    }

    public function output6()
    {
        return view('admin.reports.output6', [
            'applications' => (new NeglectedRequestsByCourse)->get()->sortBy('course.title'),
        ]);
    }

    public function output7()
    {
        return view('admin.reports.output7', [
            'applications' => DemonstratorApplication::with(['student', 'request.staff', 'request.course'])
                ->where('is_accepted', false)->get()->sortBy('student.surname'),
        ]);
    }
}
