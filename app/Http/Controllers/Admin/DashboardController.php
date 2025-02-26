<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;

class DashboardController extends Controller
{
    public function getLatestPosts()
    {
        $posts = Post::where('status', '=', 'published')
            ->with('user', 'category', 'tags', 'media')
            ->take(5)->orderBy('created_at', 'asc')->get();

        return PostResource::collection($posts);
    }
}
