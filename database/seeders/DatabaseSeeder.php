<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Profile;
use App\Models\User;
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
        Profile::factory()->create([
            'user_id' => $adminUser->id
        ]);
        $adminUser->assignRole('admin');

        $writerUser = User::factory()->create([
            'name' => 'Writer User',
            'email' => 'writer@example.com',
            'password' => bcrypt('password')
        ]);
        Profile::factory()->create([
            'user_id' => $writerUser->id
        ]);
        $writerUser->assignRole('writer');

        $writerUsers = User::factory(10)->create();

        foreach ($writerUsers as $user) {
            $user->assignRole('writer');
            Profile::factory()->create([
                'user_id' => $user->id
            ]);
        }

        $readerUser = User::factory()->create([
            'name' => 'Reader User',
            'email' => 'reader@example.com',
            'password' => bcrypt('password')
        ]);
        Profile::factory()->create([
            'user_id' => $readerUser->id
        ]);
        $readerUser->assignRole('reader');

        Category::factory(5)->create();
        Tag::factory(10)->create();

        Post::factory(100)->create()->each(function ($post) {
            $tags = Tag::inRandomOrder()->take(rand(1, 5))->pluck('id')->toArray();
            $post->tags()->attach($tags);
        });

        Comment::factory(100)->create();
        $replies = CommentReply::factory(300)->create();

        $replies->each(function ($reply) use ($replies) {
            $repliesForSameComment = $replies->where('comment_id', $reply->comment_id)->where('id', '!=', $reply->id);

            if ($repliesForSameComment->count() && rand(0, 1)) {
                $parentReply = $repliesForSameComment->random();
                $reply->update(['parent_reply_id' => $parentReply->id]);
            }
        });
    }
}
