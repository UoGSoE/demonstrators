<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->userName,
            'surname' => preg_replace('/[^a-z0-9 ]/i', ' ', $this->faker->lastName),
            'forenames' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$V2GZsZsUms87jOTJg2Dzq.TRh3rdtNrlOenpYKqu/TA5bhQRqoq9W',
            'remember_token' => Str::random(10),
            'is_admin' => false,
            'is_student' => false,
            'has_contract' => false,
            'hide_blurb' => false,
            'degree_level_id' => null,
        ];
    }

    public function staff()
    {
        return $this->state(fn ($attributes) => []);
    }

    public function student()
    {
        return $this->state(fn ($attributes) => [
            'is_student' => true,
            'has_contract' => false,
            'username' => $this->faker->randomNumber(7).$this->faker->randomLetter,
        ]);
    }

    public function admin()
    {
        return $this->state(fn ($attributes) => [
            'is_admin' => true,
        ]);
    }
}
