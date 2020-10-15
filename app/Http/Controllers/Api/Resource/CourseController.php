<?php

namespace App\Http\Controllers\Api\Resource;

use App\Models\Course;
use App\Http\Controllers\Controller;
use App\Http\Resources\Course as CourseResource;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return CourseResource::collection(Course::orderBy('code')->get());
    }
}
