<?php

namespace App;

class PermissionFilter
{
    private $permissions;
    private $filteredPermissions;

    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
        $this->filteredPermissions = $permissions;
    }

    public function remove(array $remove): self
    {
        $this->filteredPermissions = array_filter($this->filteredPermissions, function ($perm) use ($remove) {
            return !in_array($perm, $remove);
        });

        return $this;
    }

    public function only(array $only): self
    {
        $this->filteredPermissions = array_filter($this->filteredPermissions, function ($perm) use ($only) {
            return in_array($perm, $only);
        });

        return $this;
    }

    public function get(): array
    {
        return $this->filteredPermissions;
    }
}
