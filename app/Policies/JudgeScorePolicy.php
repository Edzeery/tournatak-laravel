<?php

namespace App\Policies;

use App\Models\JudgeScore;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JudgeScorePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, JudgeScore $score): bool
    {
        if ($user->can('update', $score->submission->competition)) {
            return true;
        }

        return $this->ownsScore($user, $score);
    }

    public function update(User $user, JudgeScore $score): bool
    {
        if ($user->can('judge', $score->submission->competition)) {
            return true;
        }

        return $this->ownsScore($user, $score);
    }

    private function ownsScore(User $user, JudgeScore $score): bool
    {
        return $score->judge?->user_id === $user->id;
    }
}
