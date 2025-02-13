<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    const PERMISSIONS = [
        'categories' => ['read category', 'create category', 'update category', 'destroy category'],
        'labels' => ['read labels', 'create labels', 'update labels', 'destroy labels'],
        'posts' => ['read posts', 'create posts', 'update posts', 'destroy posts'],
        'comments' => ['read comments', 'create comments', 'update comments', 'destroy comments'],
        'roles' => ['assign role', 'read roles'],
        'permissions' => ['assign permissions', 'revoke permissions']
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createPermissions();
        $this->assignPermissionsToRoles();
    }

    protected function createPermissions(): void
    {
        foreach (self::PERMISSIONS as $resource => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
            }
        }
    }

    protected function filterPermissions($permission, $remove = []): array
    {
        $permissionName = Str::singular($permission);
        if (empty($remove)) {
            $remove = ['destroy ' . $permissionName, 'update ' . $permissionName, 'create ' . $permissionName, 'read ' . $permissionName];
        }

        $filtered = array_filter(self::PERMISSIONS[$permission], function ($perm) use ($remove) {
            return !in_array($perm, $remove);
        });

        return $filtered;
    }

    protected function assignPermissionsToRoles()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $writerRole = Role::firstOrCreate(['name' => 'writer']);
        $readerRole = Role::firstOrCreate(['name' => 'reader']);

        $adminRole->givePermissionTo(Permission::all());

        $writerPermission = array_merge(
            ...[
                $this->filterPermissions('categories', ['create category', 'update category', 'destroy category']),
                $this->filterPermissions('labels', ['destroy category']),
                $this->filterPermissions('posts')
            ]
        );

        $writerRole->givePermissionTo($writerPermission);
    }
}
