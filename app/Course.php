<?php

namespace App;

use App\DemonstratorApplication;
use App\DemonstratorRequest;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function requests()
    {
        return $this->hasMany(DemonstratorRequest::class);
    }

    public function staff()
    {
        return $this->belongsToMany(User::class, 'course_staff', 'course_id', 'staff_id');
    }

    public function applications()
    {
        $applications = [];
        foreach ($this->requests as $request) {
            foreach ($request->applications as $application) {
                $applications[] = $application;
            }
        }
        return $applications;
    }
}
