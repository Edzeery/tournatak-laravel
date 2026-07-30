<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    public function created(User $user): void
    {
        $roleName = $user->role;

        if ($roleName && ! $user->hasRole($roleName)) {
            try {
                $user->assignRole($roleName);
            } catch (\Exception $e) {
                Log::warning("Failed to assign role '{$roleName}' to user {$user->id}: ".$e->getMessage());
            }
        }

        // Create default preferences if not already present
        if (! $user->preference) {
            $user->preference()->create([
                'locale' => app()->getLocale(),
            ]);
        }

        // Create security settings if not already present
        if (! $user->securitySetting) {
            $user->securitySetting()->create([]);
        }
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('role')) {
            $newRole = $user->role;

            if ($newRole) {
                try {
                    $user->syncRoles([$newRole]);
                } catch (\Exception $e) {
                    Log::warning("Failed to sync role '{$newRole}' for user {$user->id}: ".$e->getMessage());
                }
            }
        }
    }
}
