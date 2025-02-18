<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LabelRequest;
use App\Http\Resources\LabelResource;
use App\Http\Resources\PostResource;
use App\Models\Label;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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

    public function label_posts(Label $label): AnonymousResourceCollection
    {
        $this->authorize('view', $label);

        $label_posts = Post::whereHas('labels', function ($query) use ($label) {
            $query->where('label_id', $label->id);
        })->with('user', 'category', 'labels')->latest()->paginate(10);

        return PostResource::collection($label_posts);
    }

    public function show(Label $label): LabelResource
    {
        $this->authorize('view', $label);

        return new LabelResource($label);
    }

    public function store(LabelRequest $request): LabelResource
    {
        $this->authorize('create', Label::class);

        $label = Label::firstOrCreate($request->validated());

        return new LabelResource($label);
    }

    public function update(LabelRequest $request, Label $label): LabelResource
    {
        $this->authorize('update', $label);

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
