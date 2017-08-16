<?php

namespace App;

use App\DemonstratorRequest;
use App\Notifications\AcademicAcceptsStudent;
use App\Notifications\AcademicAfterStudentConfirms;
use App\Notifications\AcademicAfterStudentDeclines;
use App\Notifications\StudentConfirmWithContract;
use App\Notifications\StudentRTWInfo;
use App\User;
use Illuminate\Database\Eloquent\Model;

class DemonstratorApplication extends Model
{
    protected $guarded = [];
    protected $casts = [
        'is_accepted' => 'boolean',
        'student_confirms' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function request()
    {
        return $this->belongsTo(DemonstratorRequest::class, 'request_id');
    }

    public function scopeAccepted($query)
    {
        return $query->where('is_accepted', true);
    }

    public function isAccepted()
    {
        return $this->is_accepted;
    }

    public function isNew()
    {
        return $this->is_new;
    }

    public function markSeen()
    {
        $this->is_new = false;
        $this->save();
    }

    public function toggleAccepted()
    {
        $this->is_accepted = !$this->is_accepted;
        $this->save();
        if ($this->is_accepted) {
            $this->student->notify(new AcademicAcceptsStudent($this));
        }
    }

    public function approve()
    {
        $this->is_approved = true;
        $this->save();
    }

    public function withdraw()
    {
        if ($this->is_accepted or $this->is_approved) {
            throw new \Exception('Cannot withdraw an application that is approved/accepted.');
        }
        $this->delete();
    }

    public function studentConfirms()
    {
        $this->student_confirms = true;
        $this->save();
        $this->request->staff->notify(new AcademicAfterStudentConfirms($this));
        if ($this->student->has_contract) {
            $this->student->notify(new StudentConfirmWithContract($this));
        } else {
            //send email to admin
            $this->student->notify(new StudentRTWInfo($this));
            $this->student->notifiedAboutRTW();
        }
    }

    public function studentDeclines()
    {
        $this->request->staff->notify(new AcademicAfterStudentDeclines($this));
        $this->delete();
    }

    public function forVue()
    {
        return json_encode([
            'id' => $this->id,
            'studentName' => $this->student->full_name,
            'studentEmail' => $this->student->email,
            'is_accepted' => $this->isAccepted(),
            'requestType' => $this->request->type,
            'studentNotes' => $this->student->notes,
        ]);
    }
}
