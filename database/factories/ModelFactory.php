<?php

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
    ];
});

$factory->state(App\User::class, 'staff', function ($faker) {
    return [];
});

$factory->state(App\User::class, 'student', function ($faker) {
    return [
        'is_student' => true,
        'has_contract' => true,
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
        'title' => $faker->sentence(4),
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
        'hours_needed' => $faker->randomNumber(1),
        'demonstrators_needed' => $faker->numberBetween(1, 5),
        'starting' => Carbon::now()->subMonths(2),
        'ending' => Carbon::now()->addMonths(2),
        'skills' => $faker->paragraph(),
    ];
});
