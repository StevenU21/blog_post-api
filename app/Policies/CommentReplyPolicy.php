<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\CommentReply;
use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentReplyPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read replies');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comment $comment): bool
    {
        return $this->checkPermission($user, 'read replies');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create replies');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CommentReply $commentReply): bool
    {
        return $this->checkPermission($user, 'update replies', $commentReply);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(User $user, CommentReply $commentReply): bool
    {
        return $this->checkPermission($user, 'destroy replies', $commentReply);
    }
}
