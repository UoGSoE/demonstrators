<?php

namespace App\Http\Resources;

use App\Http\Resources\Course;
use Illuminate\Http\Resources\Json\Resource;

class Staff extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->fullName,
            'username' => $this->username,
            'email' => $this->email,
            'courses' => Course::collection($this->courses),
        ];
    }
}
