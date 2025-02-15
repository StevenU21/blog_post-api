<?php

namespace App\Policies;

use App\Classes\ValidatePolicy;
use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
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
        return $this->validatePolicy->handle($user, 'read categories');
    }

    public function view(User $user, Category $category): bool
    {
        return $this->validatePolicy->handle($user, 'read categories');
    }

    public function create(User $user): bool
    {
        return $this->validatePolicy->handle($user, 'create categories');
    }

    public function update(User $user, Category $category): bool
    {
        return $this->validatePolicy->handle($user, 'update categories');
    }

    public function destroy(User $user, Category $category): bool
    {
        return $this->validatePolicy->handle($user, 'destroy categories');
    }
}
