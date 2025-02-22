<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentReplyResource;
use App\Models\CommentReply;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentReplyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $per_page = $request->get('per_page', 10);

        $replies = CommentReply::with('user', 'comment')
            ->paginate($per_page);

        return CommentReplyResource::collection($replies);
    }

    public function commentReplies($commentId): AnonymousResourceCollection
    {
        $replies = CommentReply::where('comment_id', '=', $commentId)
            ->with('user')
            ->paginate(5);

        return CommentReplyResource::collection($replies);
    }

    public function replyResponses($parent_reply_id): AnonymousResourceCollection
    {
        $responses = CommentReply::where('parent_reply_id', '=', $parent_reply_id)
            ->with('user')
            ->paginate(5);

        return CommentReplyResource::collection($responses);
    }
}
