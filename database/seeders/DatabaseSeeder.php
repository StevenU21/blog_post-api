<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
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
        $this->call(RolesAndPermissionsSeeder::class);

        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password')
        ]);
        $adminUser->assignRole('admin');

        $writerUser = User::factory()->create([
            'name' => 'Writer User',
            'email' => 'writer@example.com',
            'password' => bcrypt('password')
        ]);
        $writerUser->assignRole('writer');

        $writerUsers = User::factory(10)->create();

        foreach ($writerUsers as $user) {
            $user->assignRole('writer');
        }

        $readerUser = User::factory()->create([
            'name' => 'Reader User',
            'email' => 'reader@example.com',
            'password' => bcrypt('password')
        ]);
        $readerUser->assignRole('reader');

        Category::factory(50)->create();
        Label::factory(10)->create();

        Post::factory()->count(100)->create()->each(function (Post $post) {
            $labels = Label::inRandomOrder()->take(rand(1, 5))->pluck('id')->toArray();
            $post->labels()->sync($labels);
        });

        Comment::factory(100)->create();
    }
}
