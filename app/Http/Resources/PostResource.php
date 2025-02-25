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
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'content' => $this->content,
            'views' => $this->views,
            'status' => $this->status,
            'cover_image' => $this->image_url,
            'category' => [
                'slug' => $this->category->slug,
                'name' => $this->category->name
            ],
            'user' => [
                'slug' => $this->user->slug,
                'name' => $this->user->name
            ],
            'labels' => $this->labels->map(function ($label) {
                return [
                    'slug' => $label->slug,
                    'name' => $label->name
                ];
            }),
            'images' => $this->getMedia('post_images')->map(function ($image) {
                return $image->getUrl();
            }),
            'created_at' => $this->created_at->isoFormat('DD-MM-YYYY HH:mm:ss'),
        ];
    }
}
