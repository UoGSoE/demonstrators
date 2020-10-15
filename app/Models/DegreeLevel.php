<?php

namespace App\Models;

use App\Models\DemonstratorRequest;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class DegreeLevel extends Model
{
    use LogsActivity;

    protected $guarded = [];
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected static $ignoreChangedAttributes = ['updated_at'];

    public function requests()
    {
        return $this->belongsToMany(DemonstratorRequest::class, 'demonstrator_request_degree_levels', 'degree_level_id', 'request_id');
    }

    public function students()
    {
        return $this->hasMany(User::class, 'degree_level_id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return ucfirst($eventName).' degree level.';
    }
}
