<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $staff = Role::create(['name' => 'staff']);
        $customer = Role::create(['name' => 'customer']);
        $guest = Role::create(['name' => 'guest']);

        // Create permissions
        $permissions = [
            'manage_users' => Permission::create(['name' => 'manage users']),
            'manage_roles' => Permission::create(['name' => 'manage roles']),
            'manage_movies' => Permission::create(['name' => 'manage movies']),
            'manage_showtimes' => Permission::create(['name' => 'manage showtimes']),
            'manage_bookings' => Permission::create(['name' => 'manage bookings']),
            'add_credit' => Permission::create(['name' => 'add credit']),
            'view_movies' => Permission::create(['name' => 'view movies']),
            'make_bookings' => Permission::create(['name' => 'make bookings']),
        ];

        // Assign permissions to roles
        $admin->givePermissionTo($permissions);
        
        $manager->givePermissionTo([
            $permissions['manage_movies'],
            $permissions['manage_showtimes'],
            $permissions['view_movies'],
        ]);

        $staff->givePermissionTo([
            $permissions['add_credit'],
            $permissions['view_movies'],
        ]);

        $customer->givePermissionTo([
            $permissions['view_movies'],
            $permissions['make_bookings'],
        ]);

        $guest->givePermissionTo([
            $permissions['view_movies'],
        ]);
    }
} 