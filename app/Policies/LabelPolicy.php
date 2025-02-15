<?php

namespace App\Policies;

use App\Models\Label;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Exceptions\UnauthorizedException;

class LabelPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    protected function validate($user, $permission)
    {
        if (!$user->hasPermissionTo($permission)) {
            return throw new UnauthorizedException(403);
        }
    }

    public function ViewAny(User $user)
    {
        $this->validate($user, 'read labels');
        return true;
    }

    public function View(User $user, Label $label)
    {

    }
}
