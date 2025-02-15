<?php

namespace App\Classes;

use App\Models\User;
use Spatie\Permission\Exceptions\UnauthorizedException;

class ValidatePolicy
{
    /**
     * Handle the permission validation.
     */
    public function handle(User $user, string $permission): bool
    {
        if (!$user->hasPermissionTo($permission)) {
            return throw new UnauthorizedException(403);
        }
        return true;
    }
}
