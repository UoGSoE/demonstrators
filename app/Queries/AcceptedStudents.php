<?php

namespace App\Queries;

use App\User;

class AcceptedStudents
{
    public function get()
    {
        return User::whereHas('applications', function ($query) {
            $query->accepted();
        })->orderBy('surname')->get();
    }
}
