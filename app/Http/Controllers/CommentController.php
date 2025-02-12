<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $comments = Comment::latest()->paginate();
        return CommentResource::collection($comments);
    }

    public function post_comments(Post $post): CommentResource
    {
        $comments = $post->comment->get();

        return new CommentResource($comments);
    }
}
