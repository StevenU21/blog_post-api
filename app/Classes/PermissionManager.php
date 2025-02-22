<?php

namespace App\Classes;

/**
 * Class PermissionManager
 *
 * Manages a permission system for resources, allowing the configuration of default permissions,
 * special permissions, and dynamic action filtering.
 *
 * @package App\Classes
 */
class PermissionManager
{
    /**
     * List of basic permissions assigned to resources.
     *
     * @var array
     */
    private $permissions;

    /**
     * List of permissions that have been filtered and organized.
     *
     * @var array
     */
    private $filteredPermissions;

    /**
     * Special permissions that override the default permissions.
     *
     * @var array
     */
    private $specialPermissions;

    /**
     * Default allowed actions on resources.
     */
    private const DEFAULT_ACTIONS = ['read', 'create', 'update', 'destroy'];

    /**
     * Class constructor.
     *
     * @param array $permissions 
     * @param array $specialPermissions
     */
    public function __construct(array $permissions, array $specialPermissions)
    {
        $this->permissions = $permissions;
        $this->specialPermissions = $specialPermissions;
        $this->filteredPermissions = $this->buildPermissions();
    }

    /**
     * Builds the list of permissions by combining base permissions with special permissions.
     *
     * @return array List of filtered permissions organized by resource.
     */
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

    /**
     * Gets the list of filtered permissions.
     *
     * @return array List of permissions organized by resource.
     */
    public function get(): array
    {
        return $this->filteredPermissions;
    }

    /**
     * Removes specific permissions from the current list of permissions.
     *
     * @param array $remove List of permissions to remove.
     * @return self Current instance with the updated permissions.
     */
    public function remove(array $remove): self
    {
        foreach ($remove as $r) {
            foreach ($this->filteredPermissions as $resource => $actions) {
                $this->filteredPermissions[$resource] = array_diff($actions, [$r]);
            }
        }

        return $this;
    }

    /**
     * Filters the current permissions to include only the specified ones.
     *
     * @param array $only List of permissions to retain.
     * @return self Current instance with the filtered permissions.
     */
    public function only(array $only): self
    {
        foreach ($this->filteredPermissions as $resource => $actions) {
            $this->filteredPermissions[$resource] = array_intersect($actions, $only);
        }

        return $this;
    }
}
