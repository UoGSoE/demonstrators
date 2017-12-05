<?php

namespace App\Queries;

use App\User;

class ConfirmedStudents
{
    public function get()
    {
        return User::whereHas('applications', function ($query) {
            $query->confirmed();
        })->orderBy('surname')->get();
    }
}
