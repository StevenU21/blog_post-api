<?php

namespace App\Policies;

use App\Classes\ValidatePolicy;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
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
        return $this->validatePolicy->handle($user, 'read posts');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Post $post): bool
    {
        return $this->validatePolicy->handle($user, 'read posts');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->validatePolicy->handle($user, 'create posts');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Post $post): bool
    {
        return $this->validatePolicy->handle($user, 'update posts');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): bool
    {
        return $this->validatePolicy->handle($user, 'destroy posts');
    }
}
