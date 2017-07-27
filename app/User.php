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
        'name', 'email', 'password', 'forenames', 'surname', 'username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'has_contract' => 'boolean',
    ];

    public function scopeStudents($query)
    {
        return $query->where('is_student', true);
    }

    public function requests()
    {
        return $this->hasMany(DemonstratorRequest::class, 'staff_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_staff', 'staff_id', 'course_id');
    }

    public function requestsForUserCourse($courseId, $type)
    {
        return $this->requests()->where('course_id', $courseId)->where('type', $type)->firstOrNew(['type' => $type, 'course_id' => $courseId]);
    }

    public function getFullNameAttribute()
    {
        return $this->forenames.' '.$this->surname;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
        $this->save();
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
        $existing = DemonstratorRequest::where('staff_id', $this->id)->where('course_id', $details['course_id'])->first();
        if ($existing) {
            foreach ($existing->applications as $application) {
                if ($application->is_approved) {
                    throw new \Exception("Cannot change hours of a request when an application has been approved.");
                }
            }
        }
        $request = DemonstratorRequest::updateOrCreate([
            'staff_id' => $this->id,
            'type' => $details['type'],
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
        if (!$hours) {
            $hours = $demonstratorRequest->hours_needed;
        }
        $application = DemonstratorApplication::updateOrCreate([
            'student_id' => $this->id,
            'request_id' => $demonstratorRequest->id,
        ], ['maximum_hours' => $hours, 'is_approved' => false, 'is_accepted' => false]);

        return $application;
    }

    public function withdraw($application)
    {
        $application->withdraw();
    }

    public function accept($demonstratorApplication)
    {
        if ($demonstratorApplication->isAccepted()) {
            return;
        }
        $demonstratorApplication->accept();
    }

    public function toggleContract()
    {
        $this->has_contract = !$this->has_contract;
        $this->save();
    }

    public static function createFromLdap($ldapData)
    {
        $user = new static([
            'username' => $ldapData['username'],
            'surname' => $ldapData['surname'],
            'forenames' => $ldapData['forenames'],
            'email' => $ldapData['email'],
            'password' => bcrypt(str_random(64))
        ]);
        $user->is_student = $user->usernameIsMatric($ldapData['username']);
        $user->save();
        return $user;
    }

    protected function usernameIsMatric($username)
    {
        if (preg_match('/^[0-9]{7}[a-z]$/i', $username)) {
            return true;
        }
        return false;
    }
}
