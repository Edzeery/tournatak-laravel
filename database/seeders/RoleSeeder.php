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

        // ── Permissions ────────────────────────────────────────────────
        $permissions = [
            // User management
            'manage users',
            'manage admin users',

            // Team management (broad — admin/captain)
            'manage teams',

            // Player management
            'manage players',

            // Competition management
            'manage competitions',
            'manage competition types',

            // Match management
            'manage matches',
            'manage goals',

            // Team sub-resource management (granular — coach)
            'manage team formations',
            'manage team tactics',
            'manage team medical',
            'manage team staff',

            // Platform content
            'manage plans',
            'manage subscriptions',
            'manage news',
            'manage settings',

            // Access
            'view dashboard',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ── Roles ──────────────────────────────────────────────────────
        // Removed: 'viewer' (redundant with 'user')
        $roles = ['admin', 'organizer', 'coach', 'captain', 'player', 'competitor', 'user'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // ── Role → Permission assignments ──────────────────────────────

        // Admin: everything
        $admin = Role::findByName('admin');
        $admin->syncPermissions($permissions);

        // Organizer: competitions, matches, goals, dashboard
        // (scoped to own competitions via CompetitionPolicy / MatchPolicy)
        $organizer = Role::findByName('organizer');
        $organizer->syncPermissions([
            'manage competitions',
            'manage matches',
            'manage goals',
            'view dashboard',
        ]);

        // Coach: team sub-resources only (scoped to own team via TeamPolicy)
        // Head coach gets full team-management panel; other staff roles remain data-only
        $coach = Role::findByName('coach');
        $coach->syncPermissions([
            'manage team formations',
            'manage team tactics',
            'manage team medical',
            'manage team staff',
            'view dashboard',
        ]);

        // Captain: broad team management (scoped to own team via TeamPolicy)
        $captain = Role::findByName('captain');
        $captain->syncPermissions([
            'manage teams',
            'view dashboard',
        ]);

        // Player, Competitor, User: no admin permissions
        // Competitor is reserved for future multi-category (Quran, tajweed, etc.)
        foreach (['player', 'competitor', 'user'] as $roleName) {
            Role::findByName($roleName)->syncPermissions([]);
        }

        // ── Migrate any existing 'viewer' users to 'user' ──────────────
        $viewerRole = Role::where('name', 'viewer')->first();
        if ($viewerRole) {
            $users = \App\Models\User::where('role', 'viewer')->get();
            foreach ($users as $user) {
                $user->removeRole('viewer');
                $user->assignRole('user');
                $user->update(['role' => 'user']);
            }
            $viewerRole->delete();
        }
    }
}
