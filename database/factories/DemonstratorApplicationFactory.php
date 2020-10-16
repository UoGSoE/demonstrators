<?php

namespace Database\Factories;

use App\Models\DemonstratorApplication;
use App\Models\DemonstratorRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DemonstratorApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DemonstratorApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'student_id' => User::factory()->student(),
            'request_id' => DemonstratorRequest::factory(),
            'is_accepted' => false,
            'is_new' => true,
            'academic_seen' => false,
        ];
    }
}
