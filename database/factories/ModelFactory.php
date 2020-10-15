<?php

use App\DemonstratorApplication;
use Carbon\Carbon;
use Illuminate\Support\Str;

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

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'username' => $faker->userName,
        'surname' => preg_replace('/[^a-z0-9 ]/i', ' ', $faker->lastName),
        'forenames' => $faker->firstName(),
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => Str::random(10),
        'is_admin' => false,
        'is_student' => false,
        'has_contract' => false,
        'hide_blurb' => false,
        'degree_level_id' => null,
    ];
});

$factory->state(App\User::class, 'staff', function ($faker) {
    return [];
});

$factory->state(App\User::class, 'student', function ($faker) {
    return [
        'is_student' => true,
        'has_contract' => false,
        'username' => $faker->randomNumber(7).$faker->randomLetter,
    ];
});

$factory->state(App\User::class, 'admin', function ($faker) {
    return [
        'is_admin' => true,
    ];
});

$factory->define(App\Course::class, function (Faker\Generator $faker) {
    return [
        'code' => 'ENG'.$faker->numberBetween(1000, 5999),
        'title' => $faker->sentence(2),
    ];
});

$factory->define(App\DegreeLevel::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->randomElement(['Ugt Yr4', 'Ugt Yr5', 'PGT (MSC)', 'PHD']),
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
        'start_date' => $faker->date(),
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
        'is_accepted' => false,
        'is_new' => true,
        'academic_seen' => false,
    ];
});

$factory->define(App\EmailLog::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function () {
            return factory(App\User::class)->states('student')->create()->id;
        },
        'notification' => $faker->word,
    ];
});
