<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    public function viewAny(Category $category): bool
    {
        if (!$category->hasPermissionTo('read categories')) {
            throw new AuthorizationException();
        }
        return true;
    }

    public function view(Category $category)
    {
        if ($category->hasPermissionTo('read categories')) {
            throw new AuthorizationException();
        }
        return true;
    }

    public function create(User $user)
    {
        if (!$user->hasPermissionTo('create categories')) {
            throw new AuthorizationException();
        }
        return true;
    }

    public function update(User $user)
    {
        if (!$user->hasPermissionTo('update categories')) {
            throw new AuthorizationException();
        }
        return true;
    }

    public function delete(User $user)
    {
        if (!$user->hasPermissionTo('destroy categories')) {
            throw new AuthorizationException();
        }
        return true;
    }
}
