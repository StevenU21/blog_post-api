<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\TagResource;
use App\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use App\Models\Tag;
use App\Models\Post;
use App\Http\Requests\TagRequest;
use Illuminate\Http\Request;

class TagController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Tag::class);

        $per_page = $request->get('per_page', 10);

        $tags = Tag::latest()->paginate($per_page);

        return TagResource::collection($tags);
    }

    public function tagPosts(Tag $tag): AnonymousResourceCollection
    {
        $this->authorize('view', $tag);

        $tagPosts = Post::whereHas('tags', function ($query) use ($tag) {
            $query->where('tag_id', '=', $tag->id)
                ->where('status', '=', 'published');
        })->with('user', 'category', 'tags', 'media')->latest()->paginate(10);

        return PostResource::collection($tagPosts);
    }

    public function show(Tag $tag): TagResource
    {
        $this->authorize('view', $tag);

        return new TagResource($tag);
    }

    public function store(TagRequest $request): TagResource
    {
        $tag = Tag::firstOrCreate($request->validated());

        return new TagResource($tag);
    }

    public function update(TagRequest $request, Tag $tag): TagResource
    {
        $tag->update($request->validated());

        return new TagResource($tag);
    }

    public function destroy(int $id): JsonResponse
    {
        $tag = Tag::findOrFail($id);

        $this->authorize('destroy', $tag);

        $tag->delete();

        return response()->json(['message' => 'Resource was deleted'], 200);
    }
}
