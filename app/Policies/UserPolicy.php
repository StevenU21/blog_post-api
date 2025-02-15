<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
        if (!$user->hasPermissionTo('read users')) {
            throw new AuthorizationException();
        }
        return true;
    }

    public function view(User $user): bool
    {
        if (!$user->hasPermissionTo('read users')) {
            throw new AuthorizationException();
        }
        return true;
    }
}
