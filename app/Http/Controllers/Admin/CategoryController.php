<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Category::class);

        $request->validate([
            'per_page' => ['integer', 'min:1'],
            'page' => ['integer', 'min:1'],
        ]);

        $per_page = $request->get('per_page', 10);
        $categories = Category::latest()->paginate($per_page);

        return CategoryResource::collection($categories);
    }

    public function categoryPosts(Category $category): AnonymousResourceCollection
    {
        $this->authorize('view', $category);

        $posts = Post::where('category_id', '=', $category->id)
            ->where('status', '=', 'published')
            ->with('user', 'category', 'tags', 'media')
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
