<?php

namespace App\Queries;

use App\User;
use App\DemonstratorApplication;
use Carbon\Carbon;

class NeglectedRequestsByCourse
{
    public function get()
    {
        return DemonstratorApplication::unaccepted()->unseen()
            ->where('created_at', '<', Carbon::now()->subdays(3))->with([
            'request.course' => function ($query) {
                $query->groupBy('courses.id');
            }
            ])->get()->unique('request_id');
    }
}
