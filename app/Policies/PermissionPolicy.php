<?php

namespace App\Policies;

use App\Classes\ValidatePolicy;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
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

    public function viewAny(User $user): bool
    {
        return $this->validatePolicy->handle($user, 'read permissions');
    }

    public function view(User $user): bool
    {
        return $this->validatePolicy->handle($user, 'read permissions');
    }

    public function assignPermissions(User $user): bool
    {
        return $this->validatePolicy->handle($user, 'assign permissions');
    }

    public function revokePermissions(User $user): bool
    {
        return $this->validatePolicy->handle($user, 'revoke permissions');
    }
}
