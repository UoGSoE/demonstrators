<?php

namespace App;

use App\DemonstratorApplication;
use App\User;
use Illuminate\Database\Eloquent\Model;

class DemonstratorRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'semester_1' => 'boolean',
        'semester_2' => 'boolean',
        'semester_3' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function applications()
    {
        return $this->hasMany(DemonstratorApplication::class, 'request_id');
    }

    public function hasApplicationFrom($user)
    {
        return $this->applications()->where('student_id', $user->id)->count();
    }

    public function applicationFrom($user)
    {
        return $this->applications()->where('student_id', $user->id)->first();
    }

    public function studentApplicationHours($user)
    {
        return $this->applicationFrom($user)->maximum_hours;
    }

    public function hasAcceptedApplicationFrom($user)
    {
        $application = $this->applicationFrom($user);
        if (!$application) {
            return false;
        }
        if (!$application->isAccepted()) {
            return false;
        }
        return true;
    }
}
