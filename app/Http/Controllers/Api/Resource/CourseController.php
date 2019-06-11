<?php

namespace App\Http\Controllers\Api\Resource;

use App\Course;
use App\Http\Resources\Course as CourseResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CourseController extends Controller
{
    public function index()
    {
        return CourseResource::collection(Course::orderBy('code')->get());
    }
}
