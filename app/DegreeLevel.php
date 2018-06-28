<?php

namespace App;

use App\DemonstratorRequest;
use Illuminate\Database\Eloquent\Model;

class DegreeLevel extends Model
{
    protected $guarded = [];

    public function requests()
    {
        return $this->belongsToMany(DemonstratorRequest::class, 'demonstrator_request_degree_levels', 'degree_level_id', 'request_id');
    }

    public function students()
    {
        return $this->hasMany(User::class, 'degree_level_id');
    }
}
