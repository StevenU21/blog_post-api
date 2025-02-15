<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CategoryPolicy
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
        if (!$user->hasPermissionTo('read categories')) {
            throw new UnauthorizedException(403);
        }
        return true;
    }

    public function view(User $user, Category $category)
    {
        if (!$user->hasPermissionTo('read categories')) {
            throw new UnauthorizedException(403);
        }
        return true;
    }

    public function create(User $user)
    {
        if (!$user->hasPermissionTo('create categories')) {
            throw new UnauthorizedException(403);
        }
        return true;
    }

    public function update(User $user, Category $category)
    {
        if (!$user->hasPermissionTo('update categories')) {
            throw new UnauthorizedException(403);
        }
        return true;
    }

    public function delete(User $user, Category $category)
    {
        if (!$user->hasPermissionTo('destroy categories')) {
            throw new UnauthorizedException(403);
        }
        return true;
    }
}
