<?php

use App\Course;
use App\DemonstratorApplication;
use App\DemonstratorRequest;
use App\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = factory(User::class)->states('admin')->create([
            'username' => 'fmi9xa',
            'surname' => 'Maciver',
            'forenames' => 'Finlay',
            'password' => bcrypt('admin')
        ]);

        factory(Course::class, 3)->create()->each(function ($course) use ($admin) {
            $course->staff()->attach(User::where('id', $admin->id)->first());
            $req1 = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'staff_id' => $admin->id, 'type' => 'Demonstrator']);
            $req2 = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'staff_id' => $admin->id, 'type' => 'Marker']);
            $req3 = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'staff_id' => $admin->id, 'type' => 'Tutor']);
            factory(DemonstratorApplication::class, 3)->create(['request_id' => $req1->id]);
            factory(DemonstratorApplication::class, 1)->create(['request_id' => $req2->id]);
        });

        factory(Course::class, 3)->create()->each(function ($course) {
            factory(User::class, 2)->states('staff')->create()->each(function ($staff) use ($course) {
                $course->staff()->attach(User::where('id', $staff->id)->first());
                $req1 = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'staff_id' => $staff->id, 'type' => 'Demonstrator']);
                $req2 = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'staff_id' => $staff->id, 'type' => 'Marker']);
                $req3 = factory(DemonstratorRequest::class)->create(['course_id' => $course->id, 'staff_id' => $staff->id, 'type' => 'Tutor']);
                factory(DemonstratorApplication::class, 3)->create(['request_id' => $req1->id]);
                factory(DemonstratorApplication::class, 1)->create(['request_id' => $req2->id]);
            });
        });
    }
}
