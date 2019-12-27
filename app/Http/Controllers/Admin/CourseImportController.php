<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Importers\CourseImporter;
use Ohffs\SimpleSpout\ExcelSheet;
use App\Http\Controllers\Controller;

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
                : "Import complete",
            'errors' => collect($errors),
        ]);
    }
}
