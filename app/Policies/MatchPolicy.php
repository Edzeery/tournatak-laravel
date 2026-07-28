<?php

namespace App\Policies;

use App\Models\Match_;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatchPolicy
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
        return $user->hasPermissionTo('manage matches')
            || $user->hasPermissionTo('manage goals')
            || $user->hasPermissionTo('view dashboard');
    }

    public function view(User $user, Match_ $match): bool
    {
        if ($user->hasRole('organizer')) {
            return $match->competition->organizer_id === $user->id;
        }

        return $user->hasPermissionTo('manage matches')
            || $user->hasPermissionTo('manage goals');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage matches');
    }

    public function update(User $user, Match_ $match): bool
    {
        if ($user->hasRole('organizer')) {
            return $match->competition->organizer_id === $user->id;
        }

        return $user->hasPermissionTo('manage matches')
            || $user->hasPermissionTo('manage goals');
    }

    public function delete(User $user, Match_ $match): bool
    {
        if ($user->hasRole('organizer')) {
            return $match->competition->organizer_id === $user->id;
        }

        return $user->hasPermissionTo('manage matches');
    }
}
