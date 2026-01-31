<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create / update superadmin
        User::updateOrCreate(
            ['email' => 'admin@cordilleraadivaylions.org'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'notification_pref' => true,
                'dark_mode' => false,
            ]
        );

        // Create additional admin
        User::firstOrCreate(
            ['email' => 'admin2@cordilleraadivaylions.org'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'notification_pref' => true,
                'dark_mode' => false,
            ]
        );

        // Create sample volunteers
        $volunteers = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'role' => 'volunteer',
                'notification_pref' => true,
                'dark_mode' => false,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password'),
                'role' => 'volunteer',
                'notification_pref' => true,
                'dark_mode' => true,
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'role' => 'volunteer',
                'notification_pref' => false,
                'dark_mode' => false,
            ],
        ];

        foreach ($volunteers as $volunteer) {
            User::firstOrCreate(
                ['email' => $volunteer['email']],
                $volunteer
            );
        }
    }
}
