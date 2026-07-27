<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Mail\VerificationEmail;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasRoles, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function preference(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function securitySetting(): HasOne
    {
        return $this->hasOne(SecuritySetting::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    public function recoveryCodes(): HasMany
    {
        return $this->hasMany(TwoFactorRecoveryCode::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'captain_id');
    }

    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class, 'organizer_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function sendPasswordResetNotification($token): void
    {
        Mail::to($this->email)->send(new ResetPasswordMail($token, $this->email));
    }

    public function sendEmailVerificationNotification(): void
    {
        $token = \Illuminate\Support\Str::random(60);
        cache()->put("email_verification_{$this->id}_{$token}", true, 60 * 60 * 24);
        Mail::to($this->email)->send(new VerificationEmail($this->id, $token));
    }
}
