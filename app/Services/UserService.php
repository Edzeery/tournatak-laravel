<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->assignRole($data['role']);
        $user->profile()->create(['full_name' => $data['name']]);
        $user->securitySetting()->create([]);

        return $user;
    }

    public function update(User $user, array $data): User
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function getValidationRules(?int $exceptId = null): array
    {
        $uniqueUsername = $exceptId
            ? "unique:users,username,{$exceptId}"
            : 'unique:users,username';
        $uniqueEmail = $exceptId
            ? "unique:users,email,{$exceptId}"
            : 'unique:users,email';

        return [
            'name' => 'required|string|max:255',
            'username' => "required|string|min:3|{$uniqueUsername}",
            'email' => "required|email|{$uniqueEmail}",
            'role' => 'required|in:competitor,captain,player,organizer,admin,user,local_organizer,coach',
        ];
    }

    public function getRoleOptions(): array
    {
        return [
            'user' => __('app.role_user'),
            'competitor' => __('app.role_competitor'),
            'captain' => __('app.role_captain'),
            'player' => __('app.role_player'),
            'organizer' => __('app.role_organizer'),
            'local_organizer' => __('app.role_local_organizer'),
            'coach' => __('app.role_coach'),
        ];
    }

    public function getCreateValidationRules(): array
    {
        return array_merge($this->getValidationRules(), [
            'password' => 'required|min:6',
        ]);
    }

    public function getUpdateValidationRules(User $user): array
    {
        return $this->getValidationRules($user->id);
    }
}
