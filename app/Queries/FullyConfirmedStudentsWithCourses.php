<?php

namespace App\Queries;

use App\Course;

class FullyConfirmedStudentsWithCourses
{
    public function get()
    {
        return Course::whereHas('requests', function ($query) {
            $query->whereHas('applications', function ($query) {
                $query->confirmed();
            });
        })->get();
    }
}
