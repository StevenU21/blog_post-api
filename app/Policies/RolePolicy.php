<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read roles');
    }
}
