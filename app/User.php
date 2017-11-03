<?php

namespace App;

use Carbon\Carbon;
use App\EmailLog;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use Illuminate\Notifications\Notifiable;
use App\Notifications\NeglectedRequests;
use App\Notifications\StudentRTWReceived;
use App\Notifications\StudentContractReady;
use App\Notifications\AcademicStudentsApplied;
use App\Notifications\StudentRequestWithdrawn;
use App\Notifications\AcademicStudentsConfirmation;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'forenames', 'surname', 'username', 'is_student'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_student' => 'boolean',
        'returned_rtw' => 'boolean',
        'has_contract' => 'boolean',
        'rtw_notified' => 'boolean',
        'hide_blurb' => 'boolean',
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

    public function emaillogs()
    {
        return $this->hasMany(EmailLog::class, 'user_id');
    }

    public function getDemonstratorApplications()
    {
        return $this->requests->flatMap(function ($request) {
            return $request->applications;
        });
    }

    public function requestsForUserCourse($courseId, $type)
    {
        $request = $this->requests->where('course_id', $courseId)->where('type', $type)->first();
        if (!$request) {
            $request = new DemonstratorRequest(['type' => $type, 'course_id' => $courseId, 'staff_id' => $this->id]);
        }
        return $request;
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

    public function newConfirmations()
    {
        return $this->getDemonstratorApplications()->filter->isNewlyConfirmed();
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
                if ($application->is_accepted) {
                    throw new \Exception("Cannot change hours of a request when an application has been accepted.");
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
            if ($existing->is_accepted) {
                throw new \Exception("Cannot change hours of an accepted application.");
            }
        }
        $application = DemonstratorApplication::updateOrCreate([
            'student_id' => $this->id,
            'request_id' => $demonstratorRequest->id,
        ], ['is_accepted' => false]);

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
            $this->notify(new StudentRTWReceived($this->forenames));
        }
    }

    public function toggleContract()
    {
        $this->has_contract = !$this->has_contract;
        $this->save();
        if ($this->has_contract) {
            $this->notify(new StudentContractReady($this->forenames));
        }
    }

    public function sendNewApplicantsEmail()
    {
        $newApplications = $this->newApplications();
        if ($newApplications->isEmpty()) {
            return;
        }
        $this->notify(new AcademicStudentsApplied($newApplications, $this->forenames));
        $newApplications->each->markSeen();
    }

    public function sendNewConfirmationsEmail()
    {
        $newConfirmations = $this->newConfirmations();
        if ($newConfirmations->isEmpty()) {
            return;
        }
        $this->notify(new AcademicStudentsConfirmation($newConfirmations, $this->forenames));
        $newConfirmations->each->markConfirmationSeen();
        $newConfirmations->filter->studentDeclined()->each->delete();
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

    public function hasAppliedFor($request)
    {
        return $this->applications->where('request_id', $request->id)->count();
    }

    public function disableBlurb()
    {
        $this->hide_blurb = true;
        $this->save();
    }

    public function getDateOf($notificationName, $request = null, $type = null)
    {
        if ($request == null) {
            $log = EmailLog::where('user_id', $this->id)->where('notification', 'like', "%$notificationName%")->latest()->first();
            if ($log) {
                return $log->created_at->format('d/m/Y H:i');
            }
            return '';
        }
        if ($request->type != $type) {
            return '';
        }
        $id = $request->applications()->where('student_id', $this->id)->latest()->first()->id;
        $log = EmailLog::where('notification', 'like', "%$notificationName%")->where('application_id', $id)->latest()->first();
        if ($log) {
            return $log->created_at->format('d/m/Y H:i');
        }
        return '';
    }

    public function requestsForCourse($course)
    {
        return $this->requests->where('course_id', $course->id);
    }

    public function getTotalConfirmedHours()
    {
        $total = 0;
        foreach ($this->applications()->confirmed()->get() as $app) {
            $total = $total + $app->request->hours_needed;
        }
        return $total;
    }

    public function notifyAboutOutstandingRequests()
    {
        $neglectedRequests = $this->requests->reject->reminder_sent;
        $onesToEmailAbout = $neglectedRequests->filter(function ($request) {
            return $request->acceptedApplications()->count() == 0;
        })->filter(function ($request) {
            return $request->applications()->where('created_at', '<', new Carbon('3 days ago'))->count() > 0;
        });
        if ($onesToEmailAbout->count() > 0) {
            $this->notify(new NeglectedRequests($onesToEmailAbout));
            $neglectedRequests->each->update(['reminder_sent' => true]);
        }
    }
}
