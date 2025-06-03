<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SeedUsersWithRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:seed-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed users with all roles (admin, manager, staff, customer, guest) and assign roles using Spatie permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'credit' => 1000.00,
                'role' => 'admin',
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'credit' => 500.00,
                'role' => 'manager',
            ],
            [
                'name' => 'Staff User',
                'email' => 'staff@example.com',
                'password' => Hash::make('password'),
                'credit' => 200.00,
                'role' => 'staff',
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@example.com',
                'password' => Hash::make('password'),
                'credit' => 50.00,
                'role' => 'customer',
            ],
            [
                'name' => 'Guest User',
                'email' => 'guest@example.com',
                'password' => Hash::make('password'),
                'credit' => 0.00,
                'role' => 'guest',
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => $data['password'],
                    'credit' => $data['credit'],
                ]
            );
            $user->assignRole($data['role']);
            $this->info("User '{$data['name']}' with role '{$data['role']}' created/updated.");
        }

        $this->info('All users seeded with roles successfully!');
    }
}
