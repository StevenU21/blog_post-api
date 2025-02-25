<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
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
            'views' => fake()->numberBetween(1, 1000),
            'status' => fake()->randomElement(['draft', 'published', 'scheduled']),
            'cover_image' => fake()->imageUrl(),
            'published_at' => function (array $attributes) {
                if ($attributes['status'] === 'scheduled') {
                    return Carbon::tomorrow()->addDays(rand(1, 30));
                }
                return null;
            },
            'category_id' => Category::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->whereHas('roles', function ($query) {
                $query->whereIn('name', ['writer', 'admin']);
            })->first()->id,
        ];
    }

    public function withLabels(array $labels): self
    {
        return $this->afterCreating(function (Post $post) use ($labels) {
            $post->labels()->sync($labels);
        });
    }
}
