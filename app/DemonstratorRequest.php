<?php

namespace App;

use App\DemonstratorApplication;
use App\User;
use Illuminate\Database\Eloquent\Model;

class DemonstratorRequest extends Model
{
    protected $guarded = [];

    public function applications()
    {
        return $this->hasMany(DemonstratorApplication::class, 'request_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
