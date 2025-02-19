<?php

namespace App\Policies;

use App\Models\Label;
use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Auth\Access\HandlesAuthorization;

class LabelPolicy
{
    use HandlesAuthorization, HasPermissionCheck;

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'read labels');
    }

    public function view(User $user, Label $label): bool
    {
        return $this->checkPermission($user, 'read labels');
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'create labels');
    }

    public function update(User $user, Label $label): bool
    {
        return $this->checkPermission($user, 'update labels');
    }

    public function destroy(User $user, Label $label): bool
    {
        return $this->checkPermission($user, 'destroy labels');
    }
}
