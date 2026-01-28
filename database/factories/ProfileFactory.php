<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => fake()->optional()->paragraph(),
            'profile_picture' => fake()->optional()->passthrough([
                'path' => 'profile-pictures/' . fake()->uuid() . '.jpg',
                'url' => fake()->imageUrl(400, 400, 'people'),
            ]),
        ];
    }
}
