<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Notifications\NewPostNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use App\Models\Post;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $request->validate([
            'sort_order' => ['in:asc,desc'],
            'per_page' => ['integer'],
            'page' => ['integer'],
            'category' => ['exists:categories,id'],
            'user' => ['exists:users,id'],
            'tags' => ['exists:tags,id']
        ]);

        if ($request->has('search')) {
            return $this->search($request);
        }

        $query = Post::where('status', 'published')
            ->with(['user', 'category', 'tags', 'media']);

        if ($request->has('category')) {
            $categorySlug = $request->category;
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->has('user')) {
            $userParam = $request->user;
            $user = User::where('id', $userParam)
                ->orWhere('slug', $userParam)
                ->first();
            if ($user) {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->has('tags')) {
            $tags = $request->get('tags');
            $tagsArray = is_array($tags) ? $tags : explode(',', $tags);
            $tags = Tag::whereIn('slug', $tagsArray)->get();
            $tagIds = $tags->pluck('id')->toArray();
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tag_id', $tagIds);
            });
        }

        if ($request->has('sort_order')) {
            $sort_order = $request->get('sort_order', 'asc');
            $query->orderBy('created_at', $sort_order);
        }

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $posts = $query->paginate($perPage, ['*'], 'page', $page);

        return PostResource::collection($posts);
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $request->validate([
            'query' => ['required']
        ]);

        $query = $request->get('query');

        $posts = Post::search($query)->paginate(10);

        $posts->load(['user', 'category', 'tags', 'media']);

        return PostResource::collection($posts);
    }

    public function userPosts(Request $request, User $user): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $request->validate([
            'per_page' => ['integer']
        ]);

        $per_page = $request->get('per_page', 5);

        $posts = $user->posts()->where('status', 'published')
            ->with('user', 'category', 'tags', 'media')
            ->paginate($per_page);

        return PostResource::collection($posts);
    }

    public function authUserPosts(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $request->validate([
            'per_page' => ['integer'],
            'published' => ['in:draft,published,scheduled']
        ]);

        $per_page = $request->get('per_page', 10);
        $status = $request->get('status', 'published');

        $posts = auth()->user()->posts()
            ->where('status', '=', $status)
            ->with('user', 'category', 'tags', 'media')
            ->paginate($per_page);

        return PostResource::collection($posts);
    }

    public function show(Post $post): PostResource
    {
        $this->authorize('view', $post);

        return new PostResource($post->load('user', 'category', 'tags', 'media'));
    }

    public function store(PostRequest $request, ImageService $imageService): PostResource
    {
        $post = Post::create($request->validated() + [
            'user_id' => Auth::id()
        ]);

        $post->tags()->sync($request->tags);

        $imageService->storeLocal(
            $post,
            'cover_image',
            $post->title,
            $request->file('cover_image')
        );

        if ($request->hasFile('images')) {
            $imageService->storeMedia($post, $request->file('images'));
        }

        if ($post->status == 'published') {
            User::whereHas('profile', function ($query) {
                $query->where('receive_notifications', true);
            })->chunk(100, function ($users) use ($post) {
                foreach ($users as $user) {
                    $user->notify(new NewPostNotification($post));
                }
            });
        }

        return new PostResource($post);
    }

    public function update(PostRequest $request, Post $post, ImageService $imageService): PostResource
    {
        $post->update($request->validated());

        $post->tags()->sync($request->tags);

        if ($request->hasFile('cover_image')) {
            $imageService->updateLocal(
                $post,
                'cover_image',
                $post->title,
                $request->file('cover_image')
            );
        }

        if ($request->hasFile('images')) {
            $imageService->updateMedia(
                $post,
                $request->input('images', []),
                $request->file('images'),
                'post_images'
            );
        }

        return new PostResource($post->load('user', 'category', 'tags', 'media'));
    }

    public function destroy(Post $post, ImageService $imageService): JsonResponse
    {
        $this->authorize('destroy', $post);

        $imageService->deleteLocal($post, 'cover_image');

        $post->delete();

        return response()->json([
            'message' => 'Post deleted'
        ], 200);
    }
}
