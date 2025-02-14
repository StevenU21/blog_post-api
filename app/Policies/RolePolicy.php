<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        if (!$user->hasPermissionTo('view roles')) {
            throw new AuthorizationException();
        }
        return true;
    }

    public function view(User $user): bool
    {
        if (!$user->hasPermissionTo('view roles')) {
            throw new AuthorizationException();
        }
        return true;
    }

    public function assignRole(User $user): bool
    {
        if (!$user->hasPermissionTo('assign roles')) {
            throw new AuthorizationException();
        }
        return true;
    }
}
