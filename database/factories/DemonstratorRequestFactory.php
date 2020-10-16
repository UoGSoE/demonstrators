<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\DemonstratorRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DemonstratorRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DemonstratorRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'course_id' => Course::factory(),
            'staff_id' => User::factory()->staff(),
            'type' => $this->faker->randomElement(['Demonstrator', 'Marker', 'Tutor']),
            'start_date' => $this->faker->date(),
            'hours_needed' => $this->faker->numberBetween(1, 20),
            'demonstrators_needed' => $this->faker->numberBetween(1, 5),
            'semester_1' => true,
            'semester_2' => $this->faker->boolean(),
            'semester_3' => $this->faker->boolean(),
            'skills' => $this->faker->paragraph(),
        ];
    }
}
