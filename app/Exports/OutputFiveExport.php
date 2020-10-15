<?php

namespace App\Exports;

use App\Models\DemonstratorRequest;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OutputFiveExport implements FromView
{
    public function view(): View
    {
        return view('admin.reports.partials.output5_table', [
            'requests' => DemonstratorRequest::doesntHave('applications')->get()->sortBy('course.title'),
        ]);
    }
}
