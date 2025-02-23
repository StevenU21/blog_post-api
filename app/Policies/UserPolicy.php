<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read users');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $this->checkPermission($user, 'update users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(User $user): bool
    {
        return $this->checkPermission($user, 'destroy users');
    }
}
