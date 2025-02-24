<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read posts');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        if ($post->status == 'draft' && $post->user_id !== auth()->id()) {
            abort(404);
        }

        return $this->checkPermission($user, 'read posts');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create posts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $this->checkPermission($user, 'update posts', $post);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(User $user, Post $post): bool
    {
        return $this->checkPermission($user, 'destroy posts', $post);
    }
}
