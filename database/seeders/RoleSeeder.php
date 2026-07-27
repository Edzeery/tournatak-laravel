<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage users', 'manage teams', 'manage players', 'manage competitions',
            'manage competition types', 'manage matches', 'manage goals',
            'manage plans', 'manage subscriptions', 'manage news',
            'view dashboard', 'manage settings', 'manage admin users',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Create roles
        $roles = ['admin', 'organizer', 'captain', 'player', 'competitor', 'viewer', 'user'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Assign all permissions to admin
        $adminRole = Role::findByName('admin');
        $adminRole->syncPermissions($permissions);

        // Organizer permissions
        $organizerRole = Role::findByName('organizer');
        $organizerRole->syncPermissions(['manage competitions', 'manage matches', 'view dashboard']);

        // Captain permissions
        $captainRole = Role::findByName('captain');
        $captainRole->syncPermissions(['manage teams', 'view dashboard']);
    }
}
