<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\TeamStaff;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
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

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage teams')
            || $user->hasPermissionTo('manage players')
            || $user->hasPermissionTo('view dashboard');
    }

    /**
     * Coach or captain may view their own team(s).
     */
    public function view(User $user, Team $team): bool
    {
        if ($user->hasRole('coach') || $user->hasRole('captain')) {
            return $this->isLinkedToTeam($user, $team);
        }

        return $user->hasPermissionTo('manage teams')
            || $user->hasPermissionTo('manage players');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage teams');
    }

    /**
     * Coach or captain may update only their own team(s).
     */
    public function update(User $user, Team $team): bool
    {
        if ($user->hasRole('coach') || $user->hasRole('captain')) {
            return $this->isLinkedToTeam($user, $team);
        }

        return $user->hasPermissionTo('manage teams');
    }

    public function delete(User $user, Team $team): bool
    {
        if ($user->hasRole('coach') || $user->hasRole('captain')) {
            return $this->isLinkedToTeam($user, $team);
        }

        return $user->hasPermissionTo('manage teams');
    }

    /**
     * Check if user is linked to team via team_staff (coach, captain, etc.)
     */
    protected function isLinkedToTeam(User $user, Team $team): bool
    {
        return TeamStaff::where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }
}
