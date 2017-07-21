<?php

namespace App;

use App\DemonstratorApplication;
use App\DemonstratorRequest;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function requests()
    {
        return $this->hasMany(DemonstratorRequest::class, 'staff_id');
    }

    public function acceptedApplications()
    {
        $applications = [];
        foreach ($this->requests as $request) {
            foreach ($request->applications as $application) {
                if ($application->isAccepted()) {
                    $applications[] = $application;
                }
            }
        }
        return $applications;
    }

    public function pendingApplications()
    {
        $applications = [];
        foreach ($this->requests as $request) {
            foreach ($request->applications as $application) {
                if (!$application->isAccepted()) {
                    $applications[] = $application;
                }
            }
        }
        return $applications;
    }

    public function requestDemonstrators($details)
    {
        $request = DemonstratorRequest::updateOrCreate([
            'staff_id' => $this->id,
            'course_id' => $details['course_id'],
        ], $details);

        return $request;
    }

    public function applyFor($demonstratorRequest, $hours = null)
    {
        $existing = DemonstratorApplication::where('student_id', $this->id)->where('request_id', $demonstratorRequest->id)->first();
        if ($existing) {
            if ($existing->is_approved or $existing->is_accepted) {
                throw new \Exception("Cannot change hours of an accepted/approved application.");
            }
        }
        $application = DemonstratorApplication::updateOrCreate([
            'student_id' => $this->id,
            'request_id' => $demonstratorRequest->id,
        ], ['maximum_hours' => $hours, 'is_approved' => false, 'is_accepted' => false]);

        return $application;
    }

    public function accept($demonstratorApplication)
    {
        if ($demonstratorApplication->isAccepted()) {
            return;
        }
        $demonstratorApplication->accept();
    }
}
