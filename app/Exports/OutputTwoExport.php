<?php

namespace App\Exports;

use App\Course;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OutputTwoExport implements FromView
{
    public function view(): View
    {
        return view('admin.reports.partials.output2_table', [
            'courses' => Course::with('staff.requests.applications.student')->get()->sortBy('title'),
        ]);
    }
}
