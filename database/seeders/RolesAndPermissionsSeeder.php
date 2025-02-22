<?php

namespace Database\Seeders;

use App\Classes\PermissionManager;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    const PERMISSIONS = [
        'categories' => [],
        'labels' => [],
        'posts' => [],
        'comments' => [],
        'replies' => [],
        'users' => [],
        'profiles' => [],
        'roles' => [],
        'permissions' => []
    ];

    const SPECIAL_PERMISSIONS = [
        'roles' => ['read roles', 'assign roles'],
        'permissions' => ['read permissions', 'assign permissions', 'revoke permissions']
    ];

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $manager = new PermissionManager(self::PERMISSIONS, self::SPECIAL_PERMISSIONS);
        $allPermissions = $manager->get();

        $this->createPermissions($allPermissions);

        $this->assignPermissionsToRoles();
    }

    protected function createPermissions($permissions): void
    {
        foreach ($permissions as $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(['name' => $perm]);
            }
        }
    }

    protected function filterPermissions($permission): PermissionManager
    {
        $permissions = self::PERMISSIONS[$permission] ?? [];
        $specialPermissions = self::SPECIAL_PERMISSIONS[$permission] ?? [];

        return new PermissionManager([$permission => $permissions], [$permission => $specialPermissions]);
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
            $this->filterPermissions('posts')->get(),
            $this->filterPermissions('comments')->get(),
            $this->filterPermissions('replies')->get(),
            $this->filterPermissions('profile')->get()
        );

        $writerRole->givePermissionTo($writerPermissions);

        $readerPermissions = array_merge(
            $this->filterPermissions('categories')->only(['read categories'])->get(),
            $this->filterPermissions('labels')->only(['read labels'])->get(),
            $this->filterPermissions('posts')->only(['read posts'])->get(),
            $this->filterPermissions('comments')->get(),
            $this->filterPermissions('replies')->get(),
            $this->filterPermissions('profile')->get()
        );

        $readerRole->givePermissionTo($readerPermissions);
    }
}
