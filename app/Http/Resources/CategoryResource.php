<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->when($user && $user->hasRole('admin') && $request->has('include_id'), $this->id),
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at->isoFormat('DD-MM-YYYY HH:mm:ss'), 
        ];
    }
}
