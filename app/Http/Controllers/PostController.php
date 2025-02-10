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

        $imagePath = Storage::disk('public')->put('post_images', $request->image);

        $post->update([
            'image' => $imagePath
        ]);

        return new PostResource($post);
    }
}
