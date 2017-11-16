<?php

namespace App;

use App\DemonstratorApplication;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DemonstratorRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'semester_1' => 'boolean',
        'semester_2' => 'boolean',
        'semester_3' => 'boolean',
        'reminder_sent' => 'boolean'
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

    public function acceptedApplications()
    {
        return $this->applications()->accepted();
    }

    public function hasApplicationFrom($user)
    {
        return $this->applications()->where('student_id', $user->id)->count();
    }

    public function applicationFrom($user)
    {
        return $this->applications->where('student_id', $user->id)->first();
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

    public function forVue()
    {
        return json_encode([
            'id' => $this->id,
            'start_date' => $this->getFormattedStartDate(),
            'type' => $this->type,
            'skills' => $this->skills,
            'staffName' => $this->staff->full_name,
            'hours_needed' => $this->hours_needed,
            'hours_training' => $this->hours_training,
            'semesters' => $this->getSemesters(),
            'userHasAppliedFor' => auth()->user()->hasAppliedFor($this),
            'userHasBeenAccepted' => $this->hasAcceptedApplicationFrom(auth()->user()),
        ]);
    }

    public function getSemesters()
    {
        if ($this->semester_1 and !$this->semester_2 and !$this->semester_3) {
            return '1';
        }
        if ($this->semester_1 and $this->semester_2 and !$this->semester_3) {
            return '1 & 2';
        }
        if ($this->semester_1 and $this->semester_2 and $this->semester_3) {
            return '1, 2 & 3';
        }
        if ($this->semester_1 and !$this->semester_2 and $this->semester_3) {
            return '1 & 3';
        }
        if (!$this->semester_1 and $this->semester_2 and !$this->semester_3) {
            return '2';
        }
        if (!$this->semester_1 and $this->semester_2 and $this->semester_3) {
            return '2 & 3';
        }
        if (!$this->semester_1 and !$this->semester_2 and $this->semester_3) {
            return '3';
        }
        return '';
    }

    public function getFormattedStartDate()
    {
        if ($this->start_date) {
            return Carbon::createFromFormat('Y-m-d', $this->start_date)->format('d/m/Y');
        }
        return '';
    }

    public function isFull()
    {
        if ($this->applications->count() > 0) {
            return $this->acceptedApplications->count() >= $this->demonstrators_needed;
        }
        return false;
    }

    public function getNumberUnfilled()
    {
        return $this->demonstrators_needed - $this->acceptedApplications->count();
    }

    public function reassignTo($user)
    {
        $this->update(['staff_id' => $user->id]);
    }
}
