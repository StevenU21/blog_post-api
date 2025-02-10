<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Label;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        Category::factory(50)->create();
        Label::factory(10)->create();

        // Crear posts y asignarles etiquetas aleatorias
        Post::factory()->count(10)->create()->each(function (Post $post)
        {
            $labels = Label::inRandomOrder()->take(rand(1, 5))->pluck('id')->toArray();
            $post->labels()->sync($labels);
        });
    }
}
