<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'global-admin' => [
                'manage groups',
                'view global dashboard'
            ],
            'admin' => [
                'view dashboard',
                'manage group',
                'manage meetups',
                'view meetups',
                'rsvp meetups',
            ]
        ];

        foreach ($data as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
                $role->givePermissionTo($permission);
            }
        }
    }
}
