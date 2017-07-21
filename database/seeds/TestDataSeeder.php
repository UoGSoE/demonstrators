<?php

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
        factory(User::class)->states('admin')->create([
            'username' => 'admin',
            'password' => bcrypt('admin')
        ]);
        factory(User::class)->states('staff')->create([
            'username' => 'staff',
            'password' => bcrypt('staff')
        ]);
        factory(User::class)->states('student')->create([
            'username' => 'student',
            'password' => bcrypt('student')
        ]);
        $staff = factory(User::class, 10)->states('staff')->create();
        factory(User::class, 10)->states('student')->create();

        foreach ($staff as $staffmember) {
            factory(DemonstratorRequest::class, 3)->create(['staff_id' => $staffmember->id]);
        }
    }
}
