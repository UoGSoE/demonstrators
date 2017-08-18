<?php

namespace App;

use App\DemonstratorApplication;
use App\DemonstratorRequest;
use App\Notifications\StudentContractReady;
use App\Notifications\StudentRTWReceived;
use App\Notifications\StudentRequestWithdrawn;
use App\Notifications\StudentsApplied;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'forenames', 'surname', 'username'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'returned_rtw' => 'boolean',
        'has_contract' => 'boolean',
        'rtw_notified' => 'boolean',
    ];

    public function getFullNameAttribute()
    {
        return $this->forenames.' '.$this->surname;
    }

    public function scopeStudents($query)
    {
        return $query->where('is_student', true);
    }

    public function scopeStaff($query)
    {
        return $query->where('is_student', false);
    }


    public function requests()
    {
        return $this->hasMany(DemonstratorRequest::class, 'staff_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_staff', 'staff_id', 'course_id');
    }

    public function applications()
    {
        return $this->hasMany(DemonstratorApplication::class, 'student_id');
    }

    public function getDemonstratorApplications()
    {
        return $this->requests->flatMap(function ($request) {
            return $request->applications;
        });
    }

    public function requestsForUserCourse($courseId, $type)
    {
        return $this->requests()->where('course_id', $courseId)->where('type', $type)->firstOrNew(['type' => $type, 'course_id' => $courseId]);
    }

    public function totalHoursAcceptedFor()
    {
        $total = 0;
        foreach ($this->applications()->accepted()->get() as $application) {
            if ($application->request) {
                $total = $total + $application->request->hours_needed;
            }
        }
        return $total;
    }

    public function acceptedApplications()
    {
        return $this->applications()->accepted()->unconfirmed()->get();
    }

    public function isAcceptedOnARequest($course)
    {
        foreach ($this->applications()->accepted()->get() as $application) {
            if ($application->request->course_id == $course) {
                return true;
            }
        }
        return false;
    }

    public function newApplications()
    {
        return $this->getDemonstratorApplications()->filter->isNew();
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
        $this->save();
    }

    public function notifiedAboutRTW()
    {
        $this->rtw_notified = true;
        $this->save();
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

    public function applyFor($demonstratorRequest)
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
        ], ['is_approved' => false, 'is_accepted' => false]);

        $this->notify(new StudentsApplied($demonstratorRequest));

        return $application;
    }

    public function withdraw($application)
    {
        $application->withdraw();
    }

    public function withdrawRequest($demonstratorRequest)
    {
        if ($demonstratorRequest->applications()->count()) {
            foreach ($demonstratorRequest->applications as $application) {
                $application->student->notify(new StudentRequestWithdrawn($demonstratorRequest));
                $application->delete();
            }
        }
        $demonstratorRequest->delete();
    }

    public function toggleRTW()
    {
        $this->returned_rtw = !$this->returned_rtw;
        $this->save();
        if ($this->returned_rtw) {
            $this->notify(new StudentRTWReceived());
        }
    }

    public function toggleContract()
    {
        $this->has_contract = !$this->has_contract;
        $this->save();
        if ($this->has_contract) {
            $this->notify(new StudentContractReady());
        }
    }

    public function sendNewApplicantsEmail()
    {
        $newApplications = $this->newApplications();
        if ($newApplications->isEmpty()) {
            return;
        }
        $this->notify(new StudentsApplied($newApplications));
        $newApplications->each->markSeen();
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

    public function hasApplications()
    {
        return $this->applications->count() > 0;
    }
}
