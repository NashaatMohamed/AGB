<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
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
            'content' => fake()->paragraphs(rand(1, 3), true),
            'image' => fake()->optional(0.3)->passthrough([
                'path' => 'posts/' . fake()->uuid() . '.jpg',
                'url' => fake()->imageUrl(800, 600),
            ]),
            'likes_count' => 0,
            'comments_count' => 0,
        ];
    }
}
