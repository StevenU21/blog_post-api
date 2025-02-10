<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Label;
use App\Models\Post;
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
            'title' => fake()->words(3, true),
            'content' => fake()->sentence(6),
            'image' => fake()->imageUrl(),
            'category_id' => Category::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id
        ];
    }

    public function withLabels(array $labels): self
    {
        return $this->afterCreating(function (Post $post) use ($labels) {
            $post->labels()->sync($labels);
        });
    }
}
