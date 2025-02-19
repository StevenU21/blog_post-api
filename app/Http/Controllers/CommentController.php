<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Post::class);

        $comments = Comment::with('user', 'post')->latest()->paginate();

        return CommentResource::collection($comments);
    }

    public function post_comments(int $postId): AnonymousResourceCollection
    {
        $post = Post::findOrFail($postId);
        $this->authorize('viewAny', $post);

        $comments = Comment::where('post_id', '=', $postId)
            ->with('user', 'post')->latest()->get();

        return CommentResource::collection($comments);
    }
    
    public function store(CommentRequest $request, int $postId): CommentResource
    {
        $comment = Comment::create($request->validated() + [
            'user_id' => Auth::id(),
            'post_id' => $postId,
        ]);

        return new CommentResource($comment);
    }

    public function update(CommentRequest $request, Comment $comment): CommentResource
    {
        $comment->update($request->validated());

        return new CommentResource($comment);
    }

    public function destroy(int $commentId): JsonResponse
    {
        $comment = Comment::findOrFail($commentId);
        $this->authorize('destroy', $comment);

        $comment->delete();

        return response()->json(['message' => 'Resource Deleted'], 200);
    }
}
