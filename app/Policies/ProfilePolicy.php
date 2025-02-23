<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function view(User $user, Profile $profile): bool
    {
        return $this->checkPermission($user, 'read profiles', $profile);
    }

    public function update(User $user, Profile $profile): bool
    {
        return $this->checkPermission($user, 'update profiles', $profile);
    }
}
