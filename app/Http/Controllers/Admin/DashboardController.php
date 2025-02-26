<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardController extends Controller
{
    public function getTotals(): JsonResponse
    {
        $total_users = User::whereHas('roles', function ($query) {
            $query->where('name', '=', 'writer');
        })->count();

        $total_posts = Post::where('status', '=', 'published')->count();

        $total_categories = Category::count();

        $total_tags = Tag::count();

        return response()->json([
            'total_users' => $total_users,
            'total_posts' => $total_posts,
            'total_categories' => $total_categories,
            'total_tags' => $total_tags
        ]);
    }

    public function getRecentPosts(): AnonymousResourceCollection
    {
        $recent_posts = Post::where('status', '=', 'published')
            ->with('user', 'category', 'tags', 'media')
            ->take(5)->orderBy('created_at', 'asc')->get();

        return PostResource::collection($recent_posts);
    }

    public function getRecentUsers(): AnonymousResourceCollection
    {
        $recent_users = User::whereHas('roles', function ($query) {
            $query->where('name', '=', 'writer');
        })->with(['profile', 'roles'])->latest()->take(10)->get();

        return UserResource::collection($recent_users);
    }

    public function getTopAuthors(): JsonResponse
    {
        $top_authors = User::withCount('posts')
            ->orderByDesc('posts_count')
            ->with(['profile', 'roles'])
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'posts_count' => $user->posts_count
                ];
            });

        return response()->json($top_authors);
    }

    public function getTopCategories(): JsonResponse
    {
        $top_categories = Category::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(5)
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'posts_count' => $category->posts_count
                ];
            });

        return response()->json($top_categories);
    }
}
