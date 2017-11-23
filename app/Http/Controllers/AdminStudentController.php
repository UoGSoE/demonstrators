<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AdminStudentController extends Controller
{
    public function store(Request $request)
    {
        User::create($request->all() + ['is_student' => true]);
        return redirect()->route('admin.user.index')->withSuccess('Saved');
    }
}
