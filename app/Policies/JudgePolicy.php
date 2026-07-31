<?php

namespace App\Policies;

use App\Models\Competition;
use App\Models\Judge;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JudgePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function create(User $user, Competition $competition): bool
    {
        return $user->can('update', $competition);
    }

    public function delete(User $user, Judge $judge): bool
    {
        return $user->can('update', $judge->competition);
    }
}
