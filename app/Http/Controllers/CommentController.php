<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $comments = Comment::with('user', 'post')->latest()->paginate();
        return CommentResource::collection($comments);
    }

    public function post_comments(int $postId): AnonymousResourceCollection
    {
        $comments = Comment::where('post_id', '=', $postId)->with('user', 'post')->latest()->get();
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

    public function update(CommentRequest $request, int $commentId): CommentResource
    {
        $comment = Comment::findOrFail($commentId);

        $comment->update($request->validated());

        return new CommentResource($comment);
    }
}
