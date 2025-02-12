<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'categories' => ['read category', 'create category', 'update category', 'destroy category'],
            'labels' => ['read labels', 'create labels', 'update labels', 'destroy labels'],
            'posts' => ['read posts', 'create posts', 'update posts', 'destroy posts'],
            'comments' => ['read comments', 'create comments', 'update comments', 'destroy comments'],
            'roles' => ['assing role'],
            'permissions' => ['assign permissions', 'revoke permissions']
        ];

        foreach ($permissions as $resource => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
            }
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $writerRole = Role::firstOrCreate(['name' => 'writer']);
        $readerRole = Role::firstOrCreate(['name' => 'reader']);

        $adminRole->givePermissionTo($permissions);
    }
}
