<?php
namespace App\Http\Controllers;

use App\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use App\Queries\ConfirmedStudents;
use App\Queries\NeglectedRequestsByCourse;
use App\Queries\AcceptedStudentsWithCourses;

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
                    'students' => (new ConfirmedStudents)->get()->sortBy('student.surname')
                ]);
            });
        })->store('xlsx');

        return response()->download(storage_path('exports/output3.xlsx'));
    }

    public function output4()
    {
        \Excel::create('output4', function ($excel) {
            $excel->sheet('New sheet', function ($sheet) {
                $sheet->loadView('admin.reports.partials.output4_table', [
                    'courses' => (new AcceptedStudentsWithCourses)->get()->sortBy('title')
                ]);
            });
        })->store('xlsx');

        return response()->download(storage_path('exports/output4.xlsx'));
    }

    public function output5()
    {
        \Excel::create('output5', function ($excel) {
            $excel->sheet('New sheet', function ($sheet) {
                $sheet->loadView('admin.reports.partials.output5_table', [
                    'requests' => DemonstratorRequest::doesntHave('applications')->get()->sortBy('course.title')
                ]);
            });
        })->store('xlsx');

        return response()->download(storage_path('exports/output5.xlsx'));
    }

    public function output6()
    {
        \Excel::create('output6', function ($excel) {
            $excel->sheet('New sheet', function ($sheet) {
                $sheet->loadView('admin.reports.partials.output6_table', [
                    'applications' => (new NeglectedRequestsByCourse)->get()->sortBy('course.title')
                ]);
            });
        })->store('xlsx');

        return response()->download(storage_path('exports/output6.xlsx'));
    }

    public function output7()
    {
        \Excel::create('output7', function ($excel) {
            $excel->sheet('New sheet', function ($sheet) {
                $sheet->loadView('admin.reports.partials.output7_table', [
                    'applications' => DemonstratorApplication::with(['student', 'request'])
                        ->where('is_accepted', false)->get()->sortBy('student.surname')
                ]);
            });
        })->store('xlsx');

        return response()->download(storage_path('exports/output7.xlsx'));
    }
}