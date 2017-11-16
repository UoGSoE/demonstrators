<?php

namespace App\Importers;

use App\Course;
use App\User;

class DemonstratorRequestImporter
{
    public function import($rows)
    {
        foreach ($rows as $row) {
            foreach ($row as $cell) {
                if (is_a($cell, 'DateTime')) {
                    $row[2] = $cell->format('Y-m-d');
                }
            }
            if (!preg_match('/^ENG/i', $row[0])) {
                continue;
            }
            $row = $this->trimRow($row);
            if (!preg_match('/^ENG/i', $row[0])) {
                continue;
            }
            $courseCode = $row[0].$row[1];
            $startDate = $row[2];
            $courseTitle = $row[3];
            $fullName = $row[4];
            if (!strpos($fullName, ' ')) {
                continue;
            }
            $userName = $row[5];
            if (!$userName) {
                continue;
            }
            $noOfDemonstrators = $row[10];
            $hoursPerDemonstrator = $row[11];
            $trainPerDemonstrator = $row[12];
            $noOfTutors = $row[13];
            $hoursPerTutor = $row[14];
            $trainPerTutor = $row[15];
            $noOfMarkers = $row[16];
            $hoursPerMarker = $row[17];
            $trainPerMarker = $row[18];
            $samePersonAsAddAc = $row[19];
            $activity = $row[20];
            $dualActivity = $row[21];
            $samePersonForDual = $row[22];
            $labContent = $row[23];
            $specialRequirements = $row[24];
            $semesters = explode(',', preg_replace('/[^0-9]+/', ',', $row[25]));
            $names = explode(' ', $fullName);
            $course = Course::firstOrCreate(['code' => $courseCode], ['title' => $courseTitle]);
            $user = User::firstOrCreate(['username' => $userName], [
                'forenames' => $names[0],
                'surname' => $names[1] ? $names[1] : 'Empty',
                'email' => preg_replace('/\s+/', '', $userName) . '@example.com',
                'password' => bcrypt(str_random(30)),
                'is_student' => false,
            ]);

            $user->courses()->sync([$course->id], false);

            $this->createRequest($noOfDemonstrators, $user, $startDate, $course->id, 'Demonstrator', $hoursPerDemonstrator, $trainPerDemonstrator, $specialRequirements, $semesters);
            $this->createRequest($noOfTutors, $user, $startDate, $course->id, 'Tutor', $hoursPerTutor, $trainPerTutor, $specialRequirements, $semesters);
            $this->createRequest($noOfMarkers, $user, $startDate, $course->id, 'Marker', $hoursPerMarker, $trainPerMarker, $specialRequirements, $semesters);
        }
    }

    protected function trimRow($row)
    {
        return array_map('trim', $row);
    }

    protected function createRequest($noOfDemonstrators, $user, $startDate, $courseId, $type, $hoursPerDemonstrator, $trainingHours, $specialRequirements, $semesters)
    {
        if ($noOfDemonstrators == 0) {
            return false;
        }
        $request = $user->requestsForUserCourse($courseId, $type);
        
        $request->start_date = $startDate ? $startDate : null;
        $request->demonstrators_needed = $noOfDemonstrators;
        $request->hours_needed = $hoursPerDemonstrator;
        $request->hours_training = $trainingHours ?: null;
        $request->skills = $specialRequirements;
        if (in_array(1, $semesters)) {
            $request->semester_1 = true;
        }
        if (in_array(2, $semesters)) {
            $request->semester_2 = true;
        }
        if (in_array(3, $semesters)) {
            $request->semester_3 = true;
        }
        $request->save();
    }
}
