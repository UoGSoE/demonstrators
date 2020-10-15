<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Importers\CourseImporter;
use Illuminate\Http\Request;
use Ohffs\SimpleSpout\ExcelSheet;

class CourseImportController extends Controller
{
    public function create()
    {
        return view('admin.courses.import');
    }

    public function store(Request $request)
    {
        $data = (new ExcelSheet)->import($request->file('spreadsheet')->getPathName());
        $errors = (new CourseImporter())->import($data);

        activity()->log('Imported courses via spreadsheet.');

        return redirect()->route('admin.courses.import.create')->with([
            'success_message' => count($errors)
                ? 'Import finished. Errors occurred. Courses without errors were added to the database.'
                : 'Import complete',
            'errors' => collect($errors),
        ]);
    }
}
