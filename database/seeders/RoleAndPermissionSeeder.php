<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view movies',
            'create movies',
            'edit movies',
            'delete movies',
            'view showtimes',
            'create showtimes',
            'edit showtimes',
            'delete showtimes',
            'view bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view movies', 'create movies', 'edit movies',
            'view showtimes', 'create showtimes', 'edit showtimes',
            'view bookings', 'edit bookings',
            'manage users'
        ]);

        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view movies',
            'view showtimes',
            'view bookings',
            'edit bookings'
        ]);

        $customerRole = Role::create(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'view movies',
            'view showtimes',
            'view bookings',
            'create bookings'
        ]);
    }
} 