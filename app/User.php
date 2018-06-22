<?php

namespace App;

use App\EmailLog;
use Carbon\Carbon;
use App\DemonstratorRequest;
use App\DemonstratorApplication;
use Illuminate\Notifications\Notifiable;
use App\Notifications\NeglectedRequests;
use App\Notifications\StudentRTWReceived;
use App\Notifications\StudentContractReady;
use App\Notifications\AcademicStudentsApplied;
use App\Notifications\StudentRequestWithdrawn;
use App\Notifications\AcademicApplicantCancelled;
use App\Notifications\AcademicStudentsConfirmation;
use App\Notifications\StudentApplicationsCancelled;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'forenames', 'surname', 'username', 'is_student', 'notes'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_student' => 'boolean',
        'returned_rtw' => 'boolean',
        'has_contract' => 'boolean',
        'rtw_notified' => 'boolean',
        'hide_blurb' => 'boolean',
        'rtw_start' => 'date',
        'rtw_end' => 'date',
        'contract_start' => 'date',
        'contract_end' => 'date:Y-m-d',
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

    public function scopeHasContract($query)
    {
        return $query->where('has_contract', true);
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

    public function hasCurrentContract()
    {
        if (!$this->has_contract) {
            return false;
        }
        if ($this->contract_end < now()) {
            return true;
        }
        return true;
    }

    public function getFormattedDate($date)
    {
        if ($this->$date) {
            return $this->$date->format('d/m/Y');
        }
        return '';
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
        if ($request->start_date) {
            $request->start_date = $request->getFormattedStartDate();
        }
        return $request;
    }

    public function acceptedApplications()
    {
        return $this->applications()->with('request.course')->accepted()->get();
    }

    public function acceptedUnconfirmedApplications()
    {
        return $this->applications()->with('request.course')->accepted()->unconfirmed()->get();
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
        $existing = DemonstratorRequest::where('staff_id', $this->id)->where('type', $details['type'])
            ->where('course_id', $details['course_id'])->first();
        if ($existing) {
            if ($existing->hours_needed != $details['hours_needed']) {
                foreach ($existing->applications as $application) {
                    if ($application->is_accepted) {
                        throw new \Exception("Cannot change hours of a request when an application has been accepted.");
                    }
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
        $existing = DemonstratorApplication::where('student_id', $this->id)
            ->where('request_id', $demonstratorRequest->id)->first();
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
                if ($application->emaillogs()->count()) {
                    $application->emaillogs->each->delete();
                }
                $application->student
                    ->notify(new StudentRequestWithdrawn($application->student->forenames, $demonstratorRequest));
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

    public function updateRTWDates($rtw_start, $rtw_end)
    {
        $this->rtw_start = $rtw_start ? $rtw_start : null;
        $this->rtw_end = $rtw_end ? $rtw_end : null;
        $this->save();
    }

    public function toggleContract()
    {
        $this->has_contract = !$this->has_contract;
        $this->save();
        if ($this->has_contract) {
            $this->notify(new StudentContractReady($this->forenames));
        }
    }

    public function updateContractDates($contract_start, $contract_end)
    {
        $this->contract_start = $contract_start ? $contract_start : null;
        $this->contract_end = $contract_end ? $contract_end : null;
        $this->save();
    }


    public function sendNewApplicantsEmail()
    {
        $newApplications = $this->newApplications();
        if ($newApplications->isEmpty()) {
            return;
        }
        $this->notify(new AcademicStudentsApplied($newApplications, $this->forenames));
        $newApplications->each->markOld();
    }

    public function sendNewConfirmationsEmail()
    {
        $newConfirmations = $this->newConfirmations();
        if ($newConfirmations->isEmpty()) {
            return;
        }
        $this->notify(new AcademicStudentsConfirmation($newConfirmations, $this->forenames));
        $newConfirmations->each->markConfirmationSeen();
        $newConfirmations->filter->studentDeclined()->each(function ($application) {
            $application->emaillogs->each->delete();
        });
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
            $log = EmailLog::where('user_id', $this->id)
                    ->where('notification', 'like', "%$notificationName%")->latest()->first();
            if ($log) {
                return $log->created_at->format('d/m/Y H:i');
            }
            return '';
        }
        if ($request->type != $type) {
            return '';
        }
        $id = $request->applications()->where('student_id', $this->id)->latest()->first()->id;
        $log = EmailLog::where('notification', 'like', "%$notificationName%")
                ->where('application_id', $id)->latest()->first();
        if ($log) {
            return $log->created_at->format('d/m/Y H:i');
        }
        if ($notificationName == 'StudentConfirm') {
            $log2 = EmailLog::where('notification', 'like', "%StudentRTWInfo%")
                ->where('application_id', $id)->latest()->first();
            if ($log2) {
                return $log2->created_at->format('d/m/Y H:i');
            }
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
            return $request->acceptedApplications()->get()->count() == 0;
        })->filter(function ($request) {
            $date = new Carbon('3 days ago');
            while ($date->isWeekend()) {
                $date->subDays(1);
            }
            return $request->applications()->unseen()->where('created_at', '<', $date)->count() > 0;
        });
        if ($onesToEmailAbout->count() > 0) {
            if (config('demonstrators.neglected_reminders')) {
                $this->notify(new NeglectedRequests($onesToEmailAbout));
            }
            $neglectedRequests->each->update(['reminder_sent' => true]);
        }
    }

    public function cancelIgnoredApplications()
    {
        $date = new Carbon('3 days ago');
        while ($date->isWeekend()) {
            $date->subDays(1);
        }
        $ignoredApplications = $this->acceptedUnconfirmedApplications()
            ->where('updated_at', '<', $date);
        if ($ignoredApplications->count() == 0) {
            return;
        }
        $this->notify(new StudentApplicationsCancelled($ignoredApplications));
        $ignoredApplications->each(function ($application) {
            $application->request->staff->notify(new AcademicApplicantCancelled($application));
            $application->emaillogs->each->delete();
            $application->delete();
        });
    }

    public function academicHasAcceptedApplicationsForCourse($course)
    {
        $requests = $this->requestsForCourse($course);
        foreach ($requests as $request) {
            if ($request->acceptedApplications()->count()) {
                return true;
            }
        }
        return false;
    }

    public function hasEmptyDates()
    {
        foreach ($this->requests as $request) {
            if (!$request->start_date) {
                return true;
            }
        }
        return false;
    }

    public function forVue()
    {
        return json_encode([
            'id' => $this->id,
            'fullName' => $this->full_name,
            'email' => $this->email,
            'username' => $this->username,
            'requests' => $this->requests,
            'applications' => $this->getDemonstratorApplications(),
            'currentCourses' => $this->courses,
            'isAdmin' => $this->is_admin,
        ]);
    }

    public function addToCourse($courseId)
    {
        $this->courses()->attach($courseId);
    }

    public function removeFromCourse($courseId)
    {
        $this->courses()->detach($courseId);
    }

    public function markApplicationsSeen($course)
    {
        $this->requestsForCourse($course)->each(function ($request) {
            if ($request->staff_id == auth()->user()->id) {
                $request->applications->each->markSeen();
            }
        });
    }

    public function toggleAdmin()
    {
        $this->is_admin = !$this->is_admin;
        $this->save();
    }
}
