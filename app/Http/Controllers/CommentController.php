<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\CommentResource;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Comment::class);

        $request->validate([
            'order_by' => ['in:asc,desc'],
            'per_page' => ['integer']
        ]);

        $order_by = $request->get('order_by', 'asc');
        $per_page = $request->get('per_page', 10);

        $comments = Comment::with('user', 'post')
            ->orderBy('created_at', $order_by)
            ->paginate($per_page);

        return CommentResource::collection($comments);
    }

    public function postComments(Request $request, Post $post): AnonymousResourceCollection
    {
        $this->authorize('view', $post);

        $request->validate([
            'order_by' => ['in:asc,desc']
        ]);

        $order_by = $request->get('order_by', 'asc');

        $comments = Comment::where('post_id', $post->id)
            ->with('user')
            ->orderBy('created_at', $order_by)
            ->paginate(10);

        return CommentResource::collection($comments);
    }

    public function store(CommentRequest $request, Post $post): CommentResource
    {
        $comment = Comment::create($request->validated() + [
            'user_id' => Auth::id(),
            'post_id' => $post->id,
        ]);

        return new CommentResource($comment);
    }

    public function update(CommentRequest $request, Comment $comment): CommentResource
    {
        $comment->update($request->validated());

        return new CommentResource($comment);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('destroy', $comment);

        $comment->delete();

        return response()->json(['message' => 'Resource Deleted'], 200);
    }
}
