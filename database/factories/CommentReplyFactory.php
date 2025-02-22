<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommentReply>
 */
class CommentReplyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => fake()->sentences(6, true),
            'user_id' => User::inRandomOrder()->first()->id,
            'comment_id' => Comment::inRandomOrder()->first()->id,
            'parent_reply_id' => CommentReply::exists() && $this->faker->boolean ? CommentReply::inRandomOrder()->first()->id : null
        ];
    }
}
