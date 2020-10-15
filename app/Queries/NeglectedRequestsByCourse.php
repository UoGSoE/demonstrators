<?php

namespace App\Queries;

use App\Models\DemonstratorApplication;
use App\Models\User;
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
