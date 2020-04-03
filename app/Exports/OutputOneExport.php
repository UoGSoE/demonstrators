<?php

namespace App\Exports;

use App\DemonstratorRequest;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OutputOneExport implements FromView
{
    public function view(): View
    {
        return view('admin.reports.partials.output1_table', [
            'requests' => DemonstratorRequest::with('staff')->get()->sortBy('course.title'),
        ]);
    }
}
