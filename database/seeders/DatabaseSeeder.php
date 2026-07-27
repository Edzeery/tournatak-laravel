<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PositionSeeder::class,
            FormationSeeder::class,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@tournatak.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
        ]);
        $admin->assignRole('admin');

        // Create demo organizer
        $organizer = User::create([
            'name' => 'Ahmed Organizer',
            'username' => 'ahmed_org',
            'email' => 'ahmed@tournatak.com',
            'password' => Hash::make('password'),
            'role' => 'organizer',
            'is_verified' => true,
        ]);
        $organizer->assignRole('organizer');

        // Create demo captain
        $captain = User::create([
            'name' => 'Mohamed Captain',
            'username' => 'moh_captain',
            'email' => 'mohamed@tournatak.com',
            'password' => Hash::make('password'),
            'role' => 'captain',
            'is_verified' => true,
        ]);
        $captain->assignRole('captain');

        // Create demo viewer
        User::create([
            'name' => 'Viewer User',
            'username' => 'viewer1',
            'email' => 'viewer@tournatak.com',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_verified' => true,
        ])->assignRole('viewer');
    }
}
