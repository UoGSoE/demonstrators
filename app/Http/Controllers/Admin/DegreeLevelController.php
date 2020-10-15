<?php

namespace App\Http\Controllers\Admin;

use App\DegreeLevel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DegreeLevelController extends Controller
{
    public function index()
    {
        return view('admin.degreelevels.index', [
            'degreeLevels' => DegreeLevel::with('requests', 'students')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.degreelevels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:degree_levels',
        ]);

        $degreeLevel = new DegreeLevel($request->all());
        $degreeLevel->save();

        return redirect()->route('admin.degreelevels.index')->with('success_message', "Degree level $degreeLevel->title saved.");
    }

    public function edit($id)
    {
        return view('admin.degreelevels.edit', [
            'degreeLevel' => DegreeLevel::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['required', Rule::unique('degree_levels')->ignore($id)],
        ]);

        $degreeLevel = DegreeLevel::findOrFail($id);
        $degreeLevel->update($request->all());

        return redirect()->route('admin.degreelevels.index')->with('success_message', "Degree level $degreeLevel->title updated.");
    }

    public function destroy($id)
    {
        $degreeLevel = DegreeLevel::findOrFail($id);
        $degreeLevel->delete();

        return redirect()->route('admin.degreelevels.index')->with('success_message', "Deleted degree level: $degreeLevel->title.");
    }
}
