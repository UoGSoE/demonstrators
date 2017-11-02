<?php

namespace App\Http\Controllers;

use App\User;
use App\Course;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\DemonstratorRequest;
use App\DemonstratorApplication;

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
            'students' => User::whereHas('applications', function ($query) {
                $query->where('is_accepted', true)->where('student_confirms', true);
            })->orderBy('surname')->get()
        ]);
    }

    public function output4()
    {
        return view('admin.reports.output4', [
            'courses' => Course::whereHas('requests', function ($query) {
                $query->whereHas('applications', function ($query) {
                    $query->confirmed();
                });
            })->get()
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
            'applications' => DemonstratorApplication::unaccepted()
            ->where('created_at', '<', Carbon::now()->subdays(3))->with([
                'request.course' => function ($query) {
                    $query->groupBy('id');
                }]
            )->get()->unique('request_id')
        ]);
    }
}
