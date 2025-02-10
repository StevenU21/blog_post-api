<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
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
        $posts = Post::latest()->paginate($per_page);
        return PostResource::collection($posts);
    }

    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }

    public function store(PostRequest $request): PostResource
    {
        $post = Post::create($request->validated() + [
            'user_id' => Auth::id()
        ]);

        $userPath = Str::slug(auth()->user()->name, '-');
        $imagePath = 'post_images/' . $userPath;
        $imageName = Str::slug($post->title, '-') . '.' . $request->image->extension();
        $imageUrl = Storage::disk('public')->putFileAs($imagePath, $request->image, $imageName);

        $post->update([
            'image' => $imageUrl
        ]);

        return new PostResource($post);
    }

    public function update(PostRequest $request, Post $post): PostResource
    {
        $post->update($request->validated());

        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $userPath = Str::slug(auth()->user()->name, '-');
        $imagePath = 'post_images/' . $userPath;
        $imageUrl = Storage::disk('public')->put($imagePath, $request->image);

        $post->update([
            'image' => $imageUrl
        ]);

        return new PostResource($post);
    }
}
