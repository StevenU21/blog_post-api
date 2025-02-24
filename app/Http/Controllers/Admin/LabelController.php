<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\LabelResource;
use App\Http\Resources\PostResource;
use Illuminate\Http\JsonResponse;
use App\Models\Label;
use App\Models\Post;
use App\Http\Requests\LabelRequest;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Label::class);

        $per_page = $request->get('per_page', 10);

        $labels = Label::latest()->paginate($per_page);

        return LabelResource::collection($labels);
    }

    public function labelPosts(Label $label): AnonymousResourceCollection
    {
        $this->authorize('view', $label);

        $label_posts = Post::whereHas('labels', function ($query) use ($label) {
            $query->where('label_id', $label->id);
        })->with('user', 'category', 'labels', 'media')->latest()->paginate(10);

        return PostResource::collection($label_posts);
    }

    public function show(Label $label): LabelResource
    {
        $this->authorize('view', $label);

        return new LabelResource($label);
    }

    public function store(LabelRequest $request): LabelResource
    {
        $label = Label::firstOrCreate($request->validated());

        return new LabelResource($label);
    }

    public function update(LabelRequest $request, Label $label): LabelResource
    {
        $label->update($request->validated());

        return new LabelResource($label);
    }

    public function destroy(int $id): JsonResponse
    {
        $label = Label::findOrFail($id);

        $this->authorize('destroy', $label);

        $label->delete();
        return response()->json(['message' => 'Resource was deleted'], 200);
    }
}
