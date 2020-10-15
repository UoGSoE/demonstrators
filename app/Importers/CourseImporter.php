<?php

namespace App\Importers;

use App\Course;
use App\User;

class CourseImporter
{
    public function import($rows)
    {
        $errors = [];
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 1;
            $courseCode = $row[0];
            $courseTitle = $row[1];

            if (! $courseCode) {
                $errors[] = "Course code missing (row $rowNumber).";
                continue;
            }
            if (! $courseTitle) {
                $errors[] = "Course title missing (row $rowNumber).";
                continue;
            }

            $existingCourse = Course::where('code', $courseCode)->count();
            if ($existingCourse) {
                $errors[] = "Course code '$courseCode' already exists in the database (found on row $rowNumber).";
            } else {
                Course::create(['code' => $courseCode, 'title' => $courseTitle]);
            }
        }

        return $errors;
    }
}
