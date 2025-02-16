<?php

namespace App\Policies;

use App\Classes\ValidatePolicy;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    protected ValidatePolicy $validatePolicy;

    public function __construct(ValidatePolicy $validatePolicy)
    {
        $this->validatePolicy = $validatePolicy;
    }

    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->validatePolicy->handle($user, 'read comments');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Comment $comment): bool
    {
        return $this->validatePolicy->handle($user, 'read comments');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->validatePolicy->handle($user, 'create comments');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): bool
    {
        return $this->validatePolicy->handle($user, 'update comments', $comment);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(User $user, Comment $comment): bool
    {
        return $this->validatePolicy->handle($user, 'destroy comments', $comment);
    }
}
