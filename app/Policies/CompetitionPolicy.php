<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetitionPolicy
{
    use HandlesAuthorization;

    /**
     * Admin bypasses all checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Anyone with 'view dashboard' can view competition list.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage competitions')
            || $user->hasPermissionTo('view dashboard');
    }

    /**
     * Organizer may view their own competitions.
     */
    public function view(User $user, Competition $competition): bool
    {
        if ($user->hasRole('organizer')) {
            return $competition->organizer_id === $user->id;
        }

        return $user->hasPermissionTo('manage competitions');
    }

    /**
     * Must have 'manage competitions' permission.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage competitions');
    }

    /**
     * Organizer may update only their own competitions.
     */
    public function update(User $user, Competition $competition): bool
    {
        if ($user->hasRole('organizer')) {
            return $competition->organizer_id === $user->id;
        }

        return $user->hasPermissionTo('manage competitions');
    }

    /**
     * Organizer may delete only their own competitions.
     */
    public function delete(User $user, Competition $competition): bool
    {
        if ($user->hasRole('organizer')) {
            return $competition->organizer_id === $user->id;
        }

        return $user->hasPermissionTo('manage competitions');
    }
}
