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

    public function post_comments(int $id): AnonymousResourceCollection
    {
        $comments = Comment::where('post_id', '=', $id)->with('user', 'post')->latest()->get();
        return CommentResource::collection($comments);
    }

    public function store(CommentRequest $request, Post $post): CommentResource
    {
        $comment = Comment::create($request->validated() + [
            'post_id' => $post,
            'user_id' => Auth::id()
        ]);

        return new CommentResource($comment);
    }
}
