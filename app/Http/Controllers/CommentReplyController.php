<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\CommentReplyResource;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use App\Models\CommentReply;
use App\Http\Requests\CommentReplyRequest;
use Illuminate\Http\Request;

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

    public function commentReplies(Comment $comment): AnonymousResourceCollection
    {
        $this->authorize('viewAny', $comment);

        $replies = CommentReply::where('comment_id', '=', $comment->id)
            ->with('user')
            ->oldest()
            ->paginate(5);

        return CommentReplyResource::collection($replies);
    }

    public function replyResponses(int $parent_reply_id): AnonymousResourceCollection
    {
        $this->authorize('view', CommentReply::class);

        $responses = CommentReply::where('parent_reply_id', '=', $parent_reply_id)
            ->with('user')
            ->oldest()
            ->paginate(5);

        return CommentReplyResource::collection($responses);
    }

    public function store(CommentReplyRequest $request, Comment $comment, int $parent_reply_id = null): CommentReplyResource
    {
        $this->authorize('create', $comment);

        $reply = CommentReply::create($request->validated() + [
            'user_id' => auth()->id(),
            'comment_id' => $comment->id,
            'parent_reply_id' => $parent_reply_id ?? null
        ]);

        return new CommentReplyResource($reply);
    }

    public function update(CommentReplyRequest $request, CommentReply $reply): CommentReplyResource
    {
        $this->authorize('update', $reply);

        $reply->update($request->validated());

        return new CommentReplyResource($reply);
    }

    public function destroy(CommentReply $reply): JsonResponse
    {
        $this->authorize('destroy', $reply);

        $reply->delete();

        return response()->json(['message' => 'Resource Deleted'], 200);
    }
}
