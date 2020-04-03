<?php

namespace App\Exports;

use App\Queries\ConfirmedStudents;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OutputThreeExport implements FromView
{
    public function view(): View
    {
        return view('admin.reports.partials.output3_table', [
            'students' => (new ConfirmedStudents)->get()->sortBy('student.surname'),
        ]);
    }
}
