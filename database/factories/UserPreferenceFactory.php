<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    protected $model = UserPreference::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'preferred_sources' => $this->faker->randomElements([1, 2, 3], 2),
            'preferred_categories' => $this->faker->randomElements([1, 2, 3], 2),
            'preferred_authors' => $this->faker->randomElements([1, 2, 3], 2),
        ];
    }
}
