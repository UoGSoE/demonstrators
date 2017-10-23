<?php

use App\DemonstratorApplication;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'username' => $faker->userName,
        'surname' => $faker->lastName,
        'forenames' => $faker->firstName(),
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'is_admin' => false,
        'is_student' => false,
        'has_contract' => false,
        'hide_blurb' => false,
    ];
});

$factory->state(App\User::class, 'staff', function ($faker) {
    return [];
});

$factory->state(App\User::class, 'student', function ($faker) {
    return [
        'is_student' => true,
        'has_contract' => false,
        'username' => $faker->randomNumber(7) . $faker->randomLetter,
    ];
});

$factory->state(App\User::class, 'admin', function ($faker) {
    return [
        'is_admin' => true,
    ];
});

$factory->define(App\Course::class, function (Faker\Generator $faker) {
    return [
        'code' => 'ENG' . $faker->numberBetween(1000, 5999),
        'title' => $faker->sentence(2),
    ];
});

$factory->define(App\DemonstratorRequest::class, function (Faker\Generator $faker) {
    return [
        'course_id' => function () {
            return factory(App\Course::class)->create()->id;
        },
        'staff_id' => function () {
            return factory(App\User::class)->states('staff')->create()->id;
        },
        'type' => $faker->randomElement(['Demonstrator', 'Marker', 'Tutor']),
        'hours_needed' => $faker->numberBetween(1, 20),
        'demonstrators_needed' => $faker->numberBetween(1, 5),
        'semester_1' => true,
        'semester_2' => $faker->boolean(),
        'semester_3' => $faker->boolean(),
        'skills' => $faker->paragraph(),
    ];
});

$factory->define(App\DemonstratorApplication::class, function (Faker\Generator $faker) {
    return [
        'student_id' => function () {
            return factory(App\User::class)->states('student')->create()->id;
        },
        'request_id' => function () {
            return factory(App\DemonstratorRequest::class)->create()->id;
        },
        'is_approved' => false,
        'is_accepted' => false,
        'is_new' => true,
    ];
});
