<?php

namespace Database\Seeders;

use App\PermissionFilter;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    const PERMISSIONS = [
        'categories' => ['read categories', 'create categories', 'update categories', 'destroy categories'],
        'labels' => ['read labels', 'create labels', 'update labels', 'destroy labels'],
        'posts' => ['read posts', 'create posts', 'update posts', 'destroy posts'],
        'comments' => ['read comments', 'create comments', 'update comments', 'destroy comments'],
        'roles' => ['assign role', 'read roles'],
        'permissions' => ['assign permissions', 'revoke permissions']
    ];

    protected function createPermissions(): void
    {
        foreach (self::PERMISSIONS as $resource => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
            }
        }
    }

    protected function filterPermissions($permission): PermissionFilter
    {
        return new PermissionFilter(self::PERMISSIONS[$permission]);
    }

    protected function assignPermissionsToRoles(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $writerRole = Role::firstOrCreate(['name' => 'writer']);
        $readerRole = Role::firstOrCreate(['name' => 'reader']);

        $adminRole->givePermissionTo(Permission::all());

        $writerPermissions = array_merge(
            $this->filterPermissions('categories')->only(['read categories'])->get(),
            $this->filterPermissions('labels')->remove(['destroy labels'])->get(),
            $this->filterPermissions('posts')->get()
        );

        $writerRole->givePermissionTo($writerPermissions);

        $readerPermissions = array_merge(
            $this->filterPermissions('categories')->only(['read categories'])->get(),
            $this->filterPermissions('labels')->only(['read labels'])->get(),
            $this->filterPermissions('posts')->only(['read posts'])->get(),
            $this->filterPermissions('comments')->get()
        );

        $readerRole->givePermissionTo($readerPermissions);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createPermissions();
        $this->assignPermissionsToRoles();
    }
}
