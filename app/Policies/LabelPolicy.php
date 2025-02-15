<?php

namespace App\Policies;

use App\Classes\ValidatePolicy;
use App\Models\Label;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Exceptions\UnauthorizedException;

class LabelPolicy
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
        return $this->validatePolicy->handle($user, 'read labels');
    }

    public function view(User $user, Label $label): bool
    {
        return $this->validatePolicy->handle($user, 'read labels');
    }

    public function create(User $user): bool
    {
        return $this->validatePolicy->handle($user, 'create labels');
    }

    public function update(User $user, Label $label): bool
    {
        return $this->validatePolicy->handle($user, 'update labels');
    }

    public function destroy(User $user, Label $label): bool
    {
        return $this->validatePolicy->handle($user, 'update labels');
    }
}
