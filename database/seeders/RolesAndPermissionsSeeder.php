<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'read category', 'create category', 'update category', 'destroy category',
            'read labels', 'create labels', 'update labels', 'destroy labels',
            'read posts', 'create posts', 'update posts', 'destroy posts',
            'read comments', 'create comments', 'update comments', 'destroy comments'
        ];
    }
}
