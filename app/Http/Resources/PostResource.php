<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this->image_url,
            'category' => $this->category->name,
            'user' => $this->user->name,
            'created_at' => $this->created_at->format('d-m-Y')
        ];
    }
}
