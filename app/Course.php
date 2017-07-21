<?php

namespace App;

use App\DemonstratorRequest;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function requests()
    {
        return $this->hasMany(DemonstratorRequest::class);
    }
}
