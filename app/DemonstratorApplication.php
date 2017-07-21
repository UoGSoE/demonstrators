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
