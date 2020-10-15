<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HooverController extends Controller
{
    public function destroy()
    {
        $students = User::students()->get()->each(function ($student) {
            $student->applications->each->delete();
            $student->emaillogs->each->delete();
            $student->delete();
        });

        activity()->log('All student data removed.');

        return redirect()->route('admin.edit_contracts')->with(['success_message' => 'All student data removed.']);
    }
}
