<?php

namespace App\Models;

use App\Models\DemonstratorRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Course extends Model
{
    use LogsActivity;
    use HasFactory;

    protected $guarded = [];
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected static $ignoreChangedAttributes = ['updated_at'];

    public function getFullTitleAttribute()
    {
        return $this->code.' '.$this->title;
    }

    public function getSubjectAttribute()
    {
        return preg_split('/\d/', $this->code)[0];
    }

    public function getCatalogueAttribute()
    {
        return preg_replace('/[A-Z]+/i', '', $this->code);
    }

    public function requests()
    {
        return $this->hasMany(DemonstratorRequest::class);
    }

    public function staff()
    {
        return $this->belongsToMany(User::class, 'course_staff', 'course_id', 'staff_id');
    }

    public function applications()
    {
        $applications = [];
        foreach ($this->requests as $request) {
            foreach ($request->applications as $application) {
                $applications[] = $application;
            }
        }

        return $applications;
    }

    public function applicationsForUser($userId)
    {
        $applications = [];
        // $this->load('requests.applications');
        foreach ($this->requests as $request) {
            if ($request->staff_id == $userId) {
                foreach ($request->applications as $application) {
                    $applications[] = $application;
                }
            }
        }

        return $applications;
    }

    public function hasRequests()
    {
        return $this->requests->count() > 0;
    }

    public function requestsAreAllAccepted()
    {
        foreach ($this->requests as $request) {
            if (! $request->isFull()) {
                return false;
            }
        }

        return true;
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return ucfirst($eventName).' course.';
    }
}
