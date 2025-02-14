<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class CategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $per_page = $request->get('per_page', 10);
        $categories = Category::latest()->paginate($per_page);
        return CategoryResource::collection($categories);
    }

    public function show(Category $category): CategoryResource
    {
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

    public function destroy(int $id): JsonResponse
    {
        Category::findOrFail($id)->delete();

        return response()->json(['message' => 'Resource was deleted'], 200);
    }
}
