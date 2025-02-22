<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentReplyRequest;
use App\Http\Resources\CommentReplyResource;
use App\Models\Comment;
use App\Models\CommentReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentReplyController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', CommentReply::class);

        $per_page = $request->get('per_page', 10);
        $order_by = $request->get('order_by', 'asc');

        $replies = CommentReply::with('user', 'comment')
            ->orderBy('created_at', $order_by)
            ->paginate($per_page);

        return CommentReplyResource::collection($replies);
    }

    public function commentReplies(int $commentId): AnonymousResourceCollection
    {
        $this->authorize('viewAny', CommentReply::class);

        $replies = CommentReply::where('comment_id', '=', $commentId)
            ->with('user')
            ->oldest()
            ->paginate(5);

        return CommentReplyResource::collection($replies);
    }

    public function replyResponses(int $parent_reply_id): AnonymousResourceCollection
    {
        $this->authorize('viewAny', CommentReply::class);

        $responses = CommentReply::where('parent_reply_id', '=', $parent_reply_id)
            ->with('user')
            ->oldest()
            ->paginate(5);

        return CommentReplyResource::collection($responses);
    }

    public function store(CommentReplyRequest $request, int $commentId, int $parent_reply_id = null): CommentReplyResource
    {
        $comment = Comment::findOrFail($commentId);
        $this->authorize('create', $comment);

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

        $this->authorize('update', $reply);

        $reply->update($request->validated());

        return new CommentReplyResource($reply);
    }

    public function destroy(int $replyId): JsonResponse
    {
        $reply = CommentReply::findOrFail($replyId);

        $this->authorize('destroy', $reply);

        $reply->delete();

        return response()->json(['message' => 'Resource Deleted'], 200);
    }
}
