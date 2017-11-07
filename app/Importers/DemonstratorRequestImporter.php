<?php

namespace App\Importers;

use App\Course;
use App\User;

class DemonstratorRequestImporter
{
    public function import($rows)
    {
        foreach ($rows as $row) {
            $row = $this->trimRow($row);
            if (!preg_match('/^ENG/i', $row[0])) {
                continue;
            }
            $courseCode = $row[0].$row[1];
            $courseTitle = $row[2];
            $userName = $row[3];
            $fullName = $row[4];
            $noOfDemonstrators = $row[5];
            $hoursPerDemonstrator = $row[6];
            $trainPerDemonstrator = $row[7];
            $noOfTutors = $row[8];
            $hoursPerTutor = $row[9];
            $trainPerTutor = $row[10];
            $noOfMarkers = $row[11];
            $hoursPerMarker = $row[12];
            $trainPerMarker = $row[13];
            $addAcStaffReq = $row[14];
            $samePersonAsAddAc = $row[15];
            $activity = $row[16];
            $dualActivity = $row[17];
            $samePersonForDual = $row[18];
            $labContent = $row[19];
            $specialRequirements = $row[20];
            $semesters = explode(',', preg_replace('/[^0-9]+/', ',', $row[21]));
            $names = explode(' ', $fullName);
            $course = Course::firstOrCreate(['code' => $courseCode], ['title' => $courseTitle]);
            $user = User::firstOrCreate(['username' => $userName], [
                'forenames' => $names[0],
                'surname' => $names[1],
                'email' => preg_replace('/\s+/', '', $userName) . '@example.com',
                'password' => bcrypt(str_random(30)),
                'is_student' => false,
            ]);

            $user->courses()->sync([$course->id], false);

            $this->createRequest($noOfDemonstrators, $user, $course->id, 'Demonstrator', $hoursPerDemonstrator, $trainPerDemonstrator, $specialRequirements, $semesters);
            $this->createRequest($noOfTutors, $user, $course->id, 'Tutor', $hoursPerTutor, $trainPerTutor, $specialRequirements, $semesters);
            $this->createRequest($noOfMarkers, $user, $course->id, 'Marker', $hoursPerMarker, $trainPerMarker, $specialRequirements, $semesters);
        }
    }

    protected function trimRow($row)
    {
        return array_map('trim', $row);
    }

    protected function createRequest($noOfDemonstrators, $user, $courseId, $type, $hoursPerDemonstrator, $trainingHours, $specialRequirements, $semesters)
    {
        if ($noOfDemonstrators == 0) {
            return false;
        }
        $request = $user->requestsForUserCourse($courseId, $type);
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
