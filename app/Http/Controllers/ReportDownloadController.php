<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Exports\OutputFiveExport;
use App\Exports\OutputFourExport;
use App\Exports\OutputOneExport;
use App\Exports\OutputSevenExport;
use App\Exports\OutputSixExport;
use App\Exports\OutputThreeExport;
use App\Exports\OutputTwoExport;
use App\Queries\AcceptedStudentsWithCourses;
use App\Queries\ConfirmedStudents;
use App\Queries\NeglectedRequestsByCourse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportDownloadController extends Controller
{
    public function output1()
    {
        return Excel::download(new OutputOneExport, 'output1.xlsx');
    }

    public function output2()
    {
        return Excel::download(new OutputTwoExport, 'output2.xlsx');
    }

    public function output3()
    {
        return Excel::download(new OutputThreeExport, 'output3.xlsx');
    }

    public function output4()
    {
        return Excel::download(new OutputFourExport, 'output4.xlsx');
    }

    public function output5()
    {
        return Excel::download(new OutputFiveExport, 'output5.xlsx');
        \Excel::create('output5', function ($excel) {
            $excel->sheet('New sheet', function ($sheet) {
                $sheet->loadView('admin.reports.partials.output5_table', [
                    'requests' => DemonstratorRequest::doesntHave('applications')->get()->sortBy('course.title'),
                ]);
            });
        })->store('xlsx');

        activity()->log('Downloaded report: Requests With No Applications');

        return response()->download(storage_path('exports/output5.xlsx'));
    }

    public function output6()
    {
        return Excel::download(new OutputSixExport, 'output6.xlsx');
        \Excel::create('output6', function ($excel) {
            $excel->sheet('New sheet', function ($sheet) {
                $sheet->loadView('admin.reports.partials.output6_table', [
                    'applications' => (new NeglectedRequestsByCourse)->get()->sortBy('course.title'),
                ]);
            });
        })->store('xlsx');

        activity()->log('Downloaded report: Unseen Applications (Older Than 3 Days)');

        return response()->download(storage_path('exports/output6.xlsx'));
    }

    public function output7()
    {
        return Excel::download(new OutputSevenExport, 'output7.xlsx');
    }
}
