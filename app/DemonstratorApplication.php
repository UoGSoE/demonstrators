<?php

namespace App;

use App\User;
use Carbon\Carbon;
use App\DemonstratorRequest;
use App\Jobs\AcademicAcceptsStudentJob;
use App\Notifications\StudentRTWInfo;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\StudentConfirmWithContract;
use App\Notifications\StudentConfirmsRTWNotified;
use App\Notifications\StudentConfirmsRTWCompleted;
use App\Notifications\AcademicAfterStudentDeclines;
use App\Notifications\AcademicAfterStudentConfirms;

class DemonstratorApplication extends Model
{
    protected $guarded = [];
    protected $casts = [
        'is_accepted' => 'boolean',
        'student_confirms' => 'boolean',
        'student_responded' => 'boolean',
        'is_new' => 'boolean',
        'academic_seen' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function request()
    {
        return $this->belongsTo(DemonstratorRequest::class, 'request_id');
    }

    public function emaillogs()
    {
        return $this->hasMany(EmailLog::class, 'application_id');
    }

    public function scopeAccepted($query)
    {
        return $query->where('is_accepted', true);
    }

    public function scopeUnaccepted($query)
    {
        return $query->where('is_accepted', false);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('student_responded', true)->where('student_confirms', true);
    }

    public function scopeUnconfirmed($query)
    {
        return $query->where('student_responded', false);
    }

    public function scopeUnseen($query)
    {
        return $query->where('academic_seen', false);
    }

    public function isAccepted()
    {
        return $this->is_accepted;
    }

    public function isNew()
    {
        return $this->is_new;
    }

    public function studentDeclined()
    {
        return !$this->student_confirms;
    }

    public function isNewlyConfirmed()
    {
        return $this->student_responded and !$this->academic_notified;
    }

    public function markOld()
    {
        $this->is_new = false;
        $this->save();
    }

    public function markConfirmationSeen()
    {
        $this->academic_notified = true;
        $this->save();
    }

    public function markSeen()
    {
        $this->academic_seen = true;
        $this->save();
    }

    public function toggleAccepted()
    {
        $this->is_accepted = !$this->is_accepted;
        $this->is_new = false;
        $this->save();
        if ($this->is_accepted) {
            AcademicAcceptsStudentJob::dispatch($this)->delay(Carbon::now()->addMinutes(30));
        }
    }

    public function withdraw()
    {
        if ($this->is_accepted) {
            throw new \Exception('Cannot withdraw an application that is accepted.');
        }
        $this->delete();
    }

    public function studentConfirms()
    {
        $this->student_confirms = true;
        $this->student_responded = true;
        $this->save();
        if ($this->student->has_contract) {
            $this->student->notify(new StudentConfirmWithContract($this, $this->student->forenames));
            return;
        }
        //send email to admin
        if (!$this->student->fresh()->rtw_notified) {
            $this->student->notify(new StudentRTWInfo($this, $this->student->forenames));
        } elseif ($this->student->fresh()->rtw_notified and !$this->student->returned_rtw) {
            $this->student->notify(new StudentConfirmsRTWNotified($this, $this->student->forenames));
        } elseif ($this->student->returned_rtw) {
            $this->student->notify(new StudentConfirmsRTWCompleted($this, $this->student->forenames));
        }
        $this->student->notifiedAboutRTW();
    }

    public function studentDeclines()
    {
        $this->student_responded = true;
        $this->save();
    }

    public function forVue()
    {
        return json_encode([
            'id' => $this->id,
            'studentName' => $this->student->full_name,
            'studentDegreeLevel' => $this->student->degreeLevel ? $this->student->degreeLevel->title : '',
            'studentEmail' => $this->student->email,
            'is_accepted' => $this->isAccepted(),
            'requestType' => $this->request->type,
            'studentNotes' => $this->student->notes,
            'hasContract' => $this->student->has_contract,
            'academicSeen' => $this->academic_seen
        ]);
    }
}
