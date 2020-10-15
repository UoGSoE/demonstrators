<?php

namespace App\Queries;

use App\DemonstratorApplication;
use App\User;
use Carbon\Carbon;

class NeglectedRequestsByCourse
{
    public function get()
    {
        return DemonstratorApplication::unaccepted()->unseen()
            ->where('created_at', '<', Carbon::now()->subdays(3))->with([
            'request.course' => function ($query) {
                $query->groupBy('id');
            },
            ])->get()->unique('request_id');
    }
}
