<?php

namespace App;

use App\DemonstratorApplication;
use Illuminate\Database\Eloquent\Model;

class DemonstratorRequest extends Model
{
    protected $guarded = [];
    protected $casts = [
        'starting' => 'date',
        'ending' => 'date',
    ];

    public function applications()
    {
        return $this->hasMany(DemonstratorApplication::class, 'request_id');
    }
}
