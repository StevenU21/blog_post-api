<?php

namespace App\Classes;

class PermissionManager
{
    private $permissions;
    private $filteredPermissions;
    private $specialPermissions;

    private const DEFAULT_ACTIONS = ['read', 'create', 'update', 'destroy'];

    public function __construct(array $permissions, array $specialPermissions)
    {
        $this->permissions = $permissions;
        $this->specialPermissions = $specialPermissions;
        $this->filteredPermissions = $this->buildPermissions();
    }

    private function buildPermissions(): array
    {
        $filtered = [];

        foreach ($this->permissions as $key => $value) {

            $resource = is_numeric($key) ? $value : $key;
            $actions = is_numeric($key) ? [] : $value;

            if (isset($this->specialPermissions[$resource])) {
                $filtered[$resource] = $this->specialPermissions[$resource];
            } else {
                $actionsList = $actions ?: self::DEFAULT_ACTIONS;
                $filtered[$resource] = array_map(
                    fn($action) => sprintf('%s %s', $action, $resource),
                    $actionsList
                );
            }
        }

        return $filtered;
    }

    public function get(): array
    {
        return $this->filteredPermissions;
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
}
