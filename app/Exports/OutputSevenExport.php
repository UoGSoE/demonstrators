<?php

namespace App\Exports;

use App\DemonstratorApplication;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OutputSevenExport implements FromView
{
    public function view(): View
    {
        return view('admin.reports.partials.output7_table', [
            'applications' => DemonstratorApplication::with(['student', 'request'])
                                ->where('is_accepted', false)
                                ->get()
                                ->sortBy('student.surname')
        ]);
    }
}
