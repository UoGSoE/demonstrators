<?php

namespace App\Importers;

use App\Course;
use App\User;

class DemonstratorRequestImporter
{
    protected $rows = [
        [
            'ENG','1003','Analogue Electronics 1','Scott Roy','5','42','','','','','','','','','','Demonstrator','','','','Open to year 4 students  - on the proviso it doesn’t have a detrimental effect on studies','1',
        ],
        [
            'ENG','1021','Electronic Engineering 1X','Scott Roy','4','50','','','','','','','','','','Demonstrator','','','',"Background in a cognate subject eg 'electronics', 'electrical', 'biomedical', computer science or physics (not 'Mech', aero or 'civil')",'1 & 2'
        ]
    ];

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

            if ($noOfDemonstrators > 0) {
                $request = $user->requestsForUserCourse($course->id, 'Demonstrator');
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

            if ($noOfTutors > 0) {
                $request = $user->requestsForUserCourse($course->id, 'Tutor');
                $request->demonstrators_needed = $noOfTutors;
                $request->hours_needed = $hoursPerTutor;
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

            if ($noOfMarkers > 0) {
                $request = $user->requestsForUserCourse($course->id, 'Marker');
                $request->demonstrators_needed = $noOfMarkers;
                $request->hours_needed = $hoursPerMarker;
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
    }

    public function trimRow($row)
    {
        return array_map('trim', $row);
    }
}
