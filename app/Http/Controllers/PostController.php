<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $per_page = $request->get('per_page', 10);
        $posts = Post::with('user', 'category', 'labels', 'media')->latest()->paginate($per_page);

        return PostResource::collection($posts);
    }

    public function user_posts(Request $request, User $user): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $per_page = $request->get('per_page', 5);
        $posts = $user->posts()->with('user', 'category', 'labels', 'media')
            ->paginate($per_page);

        return PostResource::collection($posts);
    }

    public function show(Post $post): PostResource
    {
        $this->authorize('view', $post);

        return new PostResource($post->load('user', 'category', 'labels', 'media'));
    }

    public function store(PostRequest $request, ImageService $imageService): PostResource
    {
        $this->authorize('create', Post::class);

        $post = Post::create($request->validated() + [
            'user_id' => Auth::id()
        ]);

        $post->labels()->sync($request->labels);

        $imageService->storeLocal(
            $post,
            'cover_image',
            $post->title,
            $request->file('cover_image')
        );

        if ($request->hasFile('images')) {
            $imageService->storeMedia($post, $request->file('images'));
        }

        return new PostResource($post);
    }

    public function update(PostRequest $request, Post $post, ImageService $imageService): PostResource
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        $post->labels()->sync($request->labels);

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

        return new PostResource($post->load('user', 'category', 'labels', 'media'));
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->authorize('destroy', $post);

        Storage::disk('public')->delete($post->cover_image);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted'
        ], 200);
    }
}
