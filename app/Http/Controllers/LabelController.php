<?php

namespace App\Http\Controllers;

use App\Http\Requests\LabelRequest;
use App\Http\Resources\LabelResource;
use App\Http\Resources\PostResource;
use App\Models\Label;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LabelController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $per_page = $request->get('per_page', 10);

        $labels = Label::latest()->paginate($per_page);

        return LabelResource::collection($labels);
    }

    public function label_posts(int $labelId): AnonymousResourceCollection
    {
        $label_posts = Post::whereHas('labels', function ($query) use ($labelId) {
            $query->where('label_id', $labelId);
        })->with('user', 'category')->latest()->get();

        return PostResource::collection($label_posts);
    }

    public function show(Label $label): LabelResource
    {
        return new LabelResource($label);
    }

    public function store(LabelRequest $request): LabelResource
    {
        $label = Label::create($request->validated());
        return new LabelResource($label);
    }

    public function update(LabelRequest $request, Label $label): LabelResource
    {
        $label->update($request->validated());
        return new LabelResource($label);
    }

    public function destroy(int $id): JsonResponse
    {
        Label::findOrFail($id)->delete();
        return response()->json(['message' => 'Resource was deleted'], 200);
    }
}

