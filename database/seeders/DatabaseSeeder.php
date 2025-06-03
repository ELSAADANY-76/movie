<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            RoleSeeder::class,
            RoleAndPermissionSeeder::class,
            MovieSeeder::class,
        ]);

        // Create users with specific roles

        // Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'credit' => 1000.00,
        ]);
        $adminUser->assignRole('admin');

        // Manager User
        $managerUser = User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'credit' => 500.00,
        ]);
        $managerUser->assignRole('manager');

        // Staff User
        $staffUser = User::create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'credit' => 200.00,
        ]);
        $staffUser->assignRole('staff');

        // Customer User
        $customerUser = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'credit' => 50.00,
        ]);
        $customerUser->assignRole('customer');

        // Guest User (Note: Guest typically doesn't have a user record, but creating one for demonstration)
        $guestUser = User::create([
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'password' => Hash::make('password'),
            'credit' => 0.00,
        ]);
        $guestUser->assignRole('guest');
    }
}
