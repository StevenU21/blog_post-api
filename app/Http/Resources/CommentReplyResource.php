<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentReplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => $this->user->name,
            'parent_reply_id' => $this->parent_reply_id,
            'created_at' => $this->created_at->isoFormat('DD-MM-YYYY HH:mm:ss'), 
        ];
    }
}
