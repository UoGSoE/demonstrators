<?php

namespace App\Http\Controllers;

use App\Importers\DemonstratorRequestImporter;
use Illuminate\Http\Request;
use Ohffs\SimpleSpout\ExcelSheet;

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
        return redirect()->route('import.index')->withErrors();
    }
}
