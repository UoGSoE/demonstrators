<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Queries\AcceptedStudentsWithCourses;

class OutputFourExport implements FromView
{
    public function view(): View
    {
        return view('admin.reports.partials.output4_table', [
            'courses' => (new AcceptedStudentsWithCourses)->get()->sortBy('title'),
        ]);
    }
}
