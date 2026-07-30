<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegistrationPolicy
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
        return $user->hasPermissionTo('manage registrations')
            || $user->hasPermissionTo('manage teams');
    }

    public function view(User $user, Registration $registration): bool
    {
        return $user->hasPermissionTo('manage registrations')
            || $registration->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage registrations');
    }

    public function update(User $user, Registration $registration): bool
    {
        return $user->hasPermissionTo('manage registrations');
    }

    public function delete(User $user, Registration $registration): bool
    {
        return $user->hasPermissionTo('manage registrations');
    }
}
