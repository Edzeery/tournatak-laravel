<?php

namespace App\Policies;

use App\Models\Player;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlayerPolicy
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
        return $user->hasPermissionTo('manage players');
    }

    public function view(User $user, Player $player): bool
    {
        return $user->hasPermissionTo('manage players');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage players');
    }

    public function update(User $user, Player $player): bool
    {
        return $user->hasPermissionTo('manage players');
    }

    public function delete(User $user, Player $player): bool
    {
        return $user->hasPermissionTo('manage players');
    }
}
