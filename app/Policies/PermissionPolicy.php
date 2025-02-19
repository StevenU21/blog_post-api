<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read permissions');
    }

    public function view(User $user): bool
    {
        return $this->checkPermission($user, 'read permissions');
    }

    public function assignPermissions(User $user): bool
    {
        return $this->checkPermission($user, 'assign permissions');
    }

    public function revokePermissions(User $user): bool
    {
        return $this->checkPermission($user, 'revoke permissions');
    }
}
