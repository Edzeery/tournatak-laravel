<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompetitionPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage competitions')
            || $user->hasPermissionTo('manage casual competitions')
            || $user->hasPermissionTo('view dashboard');
    }

    public function view(User $user, Competition $competition): bool
    {
        if ($user->hasRole('organizer') || $user->hasRole('local_organizer')) {
            return $competition->organizer_id === $user->id;
        }

        return $user->hasPermissionTo('manage competitions');
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('manage casual competitions')) {
            return true;
        }

        return $user->hasPermissionTo('manage competitions');
    }

    public function update(User $user, Competition $competition): bool
    {
        if ($user->hasRole('organizer') || $user->hasRole('local_organizer')) {
            return $competition->organizer_id === $user->id;
        }

        return $user->hasPermissionTo('manage competitions');
    }

    public function delete(User $user, Competition $competition): bool
    {
        if ($user->hasRole('organizer') || $user->hasRole('local_organizer')) {
            return $competition->organizer_id === $user->id;
        }

        return $user->hasPermissionTo('manage competitions');
    }
}
