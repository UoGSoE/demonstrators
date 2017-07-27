<?php

namespace App;

use App\DemonstratorRequest;
use App\User;
use Illuminate\Database\Eloquent\Model;

class DemonstratorApplication extends Model
{
    protected $guarded = [];
    protected $casts = [
        'is_accepted' => 'boolean'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function request()
    {
        return $this->belongsTo(DemonstratorRequest::class);
    }

    public function scopeAccepted($query)
    {
        return $query->where('is_accepted', true);
    }

    public function isAccepted()
    {
        return $this->is_accepted;
    }

    public function accept()
    {
        $this->is_accepted = true;
        $this->save();
    }

    public function toggleAccepted()
    {
        $this->is_accepted = !$this->is_accepted;
        $this->save();
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
}
