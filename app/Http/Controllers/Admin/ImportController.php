<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Ohffs\SimpleSpout\ExcelSheet;
use App\Http\Controllers\Controller;
use App\Importers\DemonstratorRequestImporter;

class ImportController extends Controller
{
    public function index()
    {
        return view('admin.import.index');
    }

    public function update(Request $request)
    {
        $data = (new ExcelSheet)->import($request->file('spreadsheet')->getPathName());
        $errors = (new DemonstratorRequestImporter())->import($data);
        activity()->log("Imported demonstrator requests");
        return redirect()->route('import.index')->with('success_message', 'Import successful.');
    }
}
