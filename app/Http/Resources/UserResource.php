<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'biography' => $this->profile->biography,
            'profile_picture' => $this->profile->image_url,
            'email' => $this->email,
            'role' => $this->roles->map(function ($role) {
                return [
                    'name' => $role->name
                ];
            }),
            'created_at' => $this->created_at->format('d-m-Y H:i:s')
        ];
    }
}
