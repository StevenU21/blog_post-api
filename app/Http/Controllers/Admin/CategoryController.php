<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Category::class);

        $per_page = $request->get('per_page', 10);
        $categories = Category::latest()->paginate($per_page);

        return CategoryResource::collection($categories);
    }

    public function category_posts(Category $category): AnonymousResourceCollection
    {
        $this->authorize('view', $category);

        $posts = Post::where('category_id', '=', $category->id)
            ->with('user', 'category', 'labels', 'media')
            ->latest()
            ->paginate(10);

        return PostResource::collection($posts);
    }

    public function show(Category $category): CategoryResource
    {
        $this->authorize('view', $category);

        return new CategoryResource($category);
    }

    public function store(CategoryRequest $request): CategoryResource
    {
        $category = Category::create($request->validated());

        return new CategoryResource($category);
    }

    public function update(CategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('destroy', $category);

        $category->delete();

        return response()->json(['message' => 'Resource was deleted'], 200);
    }
}
