<?php

namespace App\Http\Controllers;

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

        $per_page = $request->get('per_page', 10);
        $posts = Post::where('status', 'published')
            ->with('user', 'category', 'tags', 'media')
            ->paginate($per_page);

        return PostResource::collection($posts);
    }

    public function userPosts(Request $request, User $user): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $per_page = $request->get('per_page', 5);
        $posts = $user->posts()->where('status', 'published')
            ->with('user', 'category', 'tags', 'media')
            ->paginate($per_page);

        return PostResource::collection($posts);
    }

    public function authUserPosts(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

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
