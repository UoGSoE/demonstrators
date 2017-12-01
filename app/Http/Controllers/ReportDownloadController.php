<?php
namespace App\Http\Controllers;

use App\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use App\Queries\FullyConfirmedStudents;
use App\Queries\NeglectedRequestsByCourse;
use App\Queries\FullyConfirmedStudentsWithCourses;

class ReportDownloadController extends Controller
{
    public function output1()
    {
        return view('admin.reports.output1', [
            'courses' => Course::with('staff.requests.applications.student')->get()->sortBy('title')
        ]);
    }

    public function output2()
    {
        return view('admin.reports.output2', [
            'courses' => Course::with('staff.requests.applications.student')->get()->sortBy('title')
        ]);
    }

    public function output3()
    {
        \Excel::create('output3', function ($excel) {
            $excel->sheet('New sheet', function ($sheet) {
                $sheet->loadView('admin.reports.partials.output3_table', [
                    'students' => (new FullyConfirmedStudents)->get()->sortBy('student.surname')
                ]);
            });
        })->store('xlsx');

        return response()->download(storage_path('exports/output3.xlsx'));
    }

    public function output4()
    {
        return view('admin.reports.output4', [
            'courses' => (new FullyConfirmedStudentsWithCourses)->get()->sortBy('title')
        ]);
    }

    public function output5()
    {
        return view('admin.reports.output5', [
            'requests' => DemonstratorRequest::doesntHave('applications')->get()->sortBy('course.title')
        ]);
    }

    public function output6()
    {
        return view('admin.reports.output6', [
            'applications' => (new NeglectedRequestsByCourse)->get()->sortBy('course.title')
        ]);
    }

    public function output7()
    {
        return view('admin.reports.output7', [
            'applications' => DemonstratorApplication::with(['student', 'request'])
                ->where('is_accepted', false)->get()->sortBy('student.surname')
        ]);
    }
}