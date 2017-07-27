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
            $courseCode = $row[0].$row[1];
            $courseTitle = $row[2];
            $userName = $row[3];
            $noOfDemonstrators = $row[4];
            $hoursPerDemonstrator = $row[5];
            $trainPerDemonstrator = $row[6];
            $noOfTutors = $row[7];
            $hoursPerTutor = $row[8];
            $trainPerTutor = $row[9];
            $noOfMarkers = $row[10];
            $hoursPerMarker = $row[11];
            $trainPerMarker = $row[12];
            $addAcStaffReq = $row[13];
            $samePersonAsAddAc = $row[14];
            $activity = $row[15];
            $dualActivity = $row[16];
            $samePersonForDual = $row[17];
            $labContent = $row[18];
            $specialRequirements = $row[19];
            $semesters = explode(',', preg_replace('/[^0-9]+/', ',', $row[20]));

            $course = Course::firstOrCreate(['code' => $courseCode], ['title' => $courseTitle]);
            $user = User::firstOrCreate(['username' => $userName], [
                'forenames' => 'John',
                'surname' => 'Smith',
                'email' => preg_replace('/\s+/', '', $userName) . '@example.com',
                'password' => bcrypt(str_random(30)),
            ]);

            $this->createRequest($noOfDemonstrators, $user, $course->id, 'Demonstrator', $hoursPerDemonstrator, $specialRequirements, $semesters);
            $this->createRequest($noOfTutors, $user, $course->id, 'Tutor', $hoursPerTutor, $specialRequirements, $semesters);
            $this->createRequest($noOfMarkers, $user, $course->id, 'Marker', $hoursPerMarker, $specialRequirements, $semesters);
        }
    }

    protected function trimRow($row)
    {
        return array_map('trim', $row);
    }

    protected function createRequest($noOfDemonstrators, $user, $courseId, $type, $hoursPerDemonstrator, $specialRequirements, $semesters)
    {
        if ($noOfDemonstrators == 0) {
            return false;
        }
        $request = $user->requestsForUserCourse($courseId, $type);
        $request->demonstrators_needed = $noOfDemonstrators;
        $request->hours_needed = $hoursPerDemonstrator;
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
