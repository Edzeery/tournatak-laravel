<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthService
{
    public function register(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['is_verified'] = false;

        $user = User::create($data);
        $user->assignRole($data['role']);

        SecurityActivityLogger::accountCreated($user);
        $user->sendEmailVerificationNotification();

        return $user;
    }

    public function login(string $identifier, string $password, bool $remember = false): array
    {
        $throttleKey = 'login:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return [
                'success' => false,
                'throttled' => true,
                'seconds' => $seconds,
            ];
        }

        $credentials = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? ['email' => $identifier, 'password' => $password]
            : ['username' => $identifier, 'password' => $password];

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_verified) {
                Auth::logout();
                return [
                    'success' => false,
                    'unverified' => true,
                ];
            }

            SecurityActivityLogger::login($user);

            if ($user->securitySetting?->twofa_app) {
                Auth::logout();
                return [
                    'success' => false,
                    'requires_2fa' => true,
                    'user_id' => $user->id,
                ];
            }

            session()->regenerate();

            return [
                'success' => true,
                'user' => $user,
                'intended' => route('admin.dashboard'),
            ];
        }

        SecurityActivityLogger::failedLogin($identifier);
        RateLimiter::hit($throttleKey, 60);

        return [
            'success' => false,
            'invalid_credentials' => true,
        ];
    }

    public function logout(): void
    {
        $user = Auth::user();
        if ($user) {
            SecurityActivityLogger::logout($user);
        }

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    public function getRegisterValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:3|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:user,competitor,captain,player,organizer,admin',
        ];
    }
}
