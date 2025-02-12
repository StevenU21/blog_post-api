<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    const PERMISSIONS = [
        'categories' => ['read category', 'create category', 'update category', 'destroy category'],
        'labels' => ['read labels', 'create labels', 'update labels', 'destroy labels'],
        'posts' => ['read posts', 'create posts', 'update posts', 'destroy posts'],
        'comments' => ['read comments', 'create comments', 'update comments', 'destroy comments'],
        'roles' => ['assign role'],
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

    protected function createPermissions()
    {
        foreach (self::PERMISSIONS as $resource => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
            }
        }
    }

    protected function assignPermissionsToRoles()
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $writerRole = Role::firstOrCreate(['name' => 'writer']);
        $readerRole = Role::firstOrCreate(['name' => 'reader']);

        $adminRole->givePermissionTo(Permission::all());

        $categoryFilter = array_filter(self::PERMISSIONS['categories'], function ($perm) {
            return $perm !== 'create category';
        });

        $writerPermission = array_merge([
            $categoryFilter,
            self::PERMISSIONS['labels'],
            self::PERMISSIONS['posts']
        ]);

        $writerRole->givePermissionTo($writerPermission);
    }
}
