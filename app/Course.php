<?php

namespace App;

use App\DemonstratorApplication;
use App\DemonstratorRequest;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $guarded = [];

    public function getFullTitleAttribute()
    {
        return $this->code.' '.$this->title;
    }

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

    public function hasRequests()
    {
        return $this->requests()->count() > 0;
    }

    public function requestsAreAllAccepted()
    {
        foreach ($this->requests as $request) {
            if (!$request->isFull()) {
                return false;
            }
        }
        return true;
    }
}
