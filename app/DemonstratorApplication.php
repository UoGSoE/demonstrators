<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DemonstratorApplication extends Model
{
    protected $guarded = [];
    protected $casts = [
        'is_accepted' => 'boolean'
    ];

    public function isAccepted()
    {
        return $this->is_accepted;
    }

    public function accept()
    {
        $this->is_accepted = true;
        $this->save();
    }
}
