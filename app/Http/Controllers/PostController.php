<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $per_page = $request->get('per_page', 10);
        $posts = Post::with('user', 'category')->latest()->paginate($per_page);
        return PostResource::collection($posts);
    }

    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    private function manage_image($post, $image)
    {
        $userPath = Str::slug(auth()->user()->name, '-');
        $imagePath = 'post_images/' . $userPath;
        $imageName = Str::slug($post->title, '-') . '.' . $image->extension();
        $imageUrl = Storage::disk('public')->putFileAs($imagePath, $image, $imageName);

        return $post->update(['image' => $imageUrl]);
    }

    public function store(PostRequest $request): PostResource
    {
        $post = Post::create($request->validated() + [
            'user_id' => Auth::id()
        ]);

        $image = $request->image;
        $this->manage_image($post, $image);

        return new PostResource($post);
    }

    public function update(PostRequest $request, Post $post): PostResource
    {
        $post->update($request->validated());
        $image = $post->image;

        if ($image) {
            Storage::disk('public')->delete($post->image);
            $this->manage_image($post, $image);
        }

        return new PostResource($post);
    }

    public function destroy(int $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        Storage::disk('public')->delete($post->image);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted'
        ], 200);
    }
}
