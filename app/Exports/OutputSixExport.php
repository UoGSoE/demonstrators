<?php

namespace App\Exports;

use App\Queries\NeglectedRequestsByCourse;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OutputSixExport implements FromView
{
    public function view(): View
    {
        return view('admin.reports.partials.output6_table', [
            'applications' => (new NeglectedRequestsByCourse)->get()->sortBy('course.title'),
        ]);
    }
}
