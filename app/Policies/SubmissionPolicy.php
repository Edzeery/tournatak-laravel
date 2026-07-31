<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubmissionPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function update(User $user, Submission $submission): bool
    {
        return $user->can('update', $submission->competition);
    }

    public function delete(User $user, Submission $submission): bool
    {
        return $user->can('update', $submission->competition);
    }
}
