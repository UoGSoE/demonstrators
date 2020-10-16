<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\EmailLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->student(),
            'notification' => $this->faker->word,
        ];
    }
}
