<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentReplyRequest;
use App\Http\Resources\CommentReplyResource;
use App\Models\CommentReply;
use Illuminate\Http\JsonResponse;
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

    public function commentReplies(int $commentId): AnonymousResourceCollection
    {
        $replies = CommentReply::where('comment_id', '=', $commentId)
            ->with('user')
            ->paginate(5);

        return CommentReplyResource::collection($replies);
    }

    public function replyResponses(int $parent_reply_id): AnonymousResourceCollection
    {
        $responses = CommentReply::where('parent_reply_id', '=', $parent_reply_id)
            ->with('user')
            ->paginate(5);

        return CommentReplyResource::collection($responses);
    }

    public function store(CommentReplyRequest $request, int $commentId, int $parent_reply_id = null): CommentReplyResource
    {
        $reply = CommentReply::create($request->validated() + [
            'user_id' => auth()->id(),
            'comment_id' => $commentId,
            'parent_reply_id' => $parent_reply_id ?? null
        ]);

        return new CommentReplyResource($reply);
    }

    public function update(CommentReplyRequest $request, int $replyId): CommentReplyResource
    {
        $reply = CommentReply::findOrFail($replyId);

        $reply->update($request->validated());

        return new CommentReplyResource($reply);
    }

    public function destroy(int $replyId): JsonResponse
    {
        $reply = CommentReply::findOrFail($replyId);

        $reply->delete();

        return response()->json(['message' => 'Resource Deleted'], 200);
    }
}
