<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function getTopPosts(): AnonymousResourceCollection
    {
        $top_posts = Post::orderBy('views', 'desc')
            ->with('user', 'category', 'tags', 'media')
            ->take(5)->get();

        return PostResource::collection($top_posts);
    }

    public function getNewUsersByDateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => ['nullable', 'date', 'before:today'],
            'end_date' => ['nullable', 'date', 'after:start_date']
        ]);

        $startDate = $request->query('start_date') ? Carbon::createFromFormat('Y-m-d', $request->query('start_date')) : null;
        $endDate = $request->query('end_date') ? Carbon::createFromFormat('Y-m-d', $request->query('end_date')) : null;

        $query = User::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $users = $query->count();

        return response()->json($users);
    }

    public function getNewUsersByFilter(Request $request): JsonResponse
    {
        $request->validate([
            'filter' => ['in:current_week,last_week,current_month,last_month']
        ]);

        $filter = $request->query('filter', 'current_week');

        $filterDates = match ($filter) {
            'current_week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'last_week' => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'current_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'last_month' => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            default => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
        };

        [$startDate, $endDate] = $filterDates;

        $query = User::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $users = $query->count();

        return response()->json($users);
    }
}
