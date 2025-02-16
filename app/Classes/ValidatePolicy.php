<?php

namespace App\Classes;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Exceptions\UnauthorizedException;

class ValidatePolicy
{
    /**
     * Handle the permission validation.
     */
    public function handle(User $user, string $permission, ?Model $model = null): bool
    {
        if (!$user->hasPermissionTo($permission)) {
            return throw new UnauthorizedException(403);
        }

        if ($model && $user->id !== $model->user_id) {
            throw new UnauthorizedException(403);
        }

        // admin is pro
        if ($user->hasRole('admin')) {
            return true;
        }

        return true;
    }
}
