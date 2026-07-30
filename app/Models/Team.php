<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'logo',
        'captain_id',
        'sport_id',
        'points',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'registrations')->withPivot('status');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(TeamStaff::class);
    }

    public function activeStaff(): HasMany
    {
        return $this->hasMany(TeamStaff::class)->where('is_active', true);
    }

    public function formations(): HasMany
    {
        return $this->hasMany(Formation::class);
    }

    public function tactics(): HasMany
    {
        return $this->hasMany(TeamTactic::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(TeamMedicalRecord::class);
    }

    public function seasonStats(): HasMany
    {
        return $this->hasMany(TeamSeasonStat::class);
    }

    public function getHeadCoachAttribute(): ?User
    {
        return $this->staff()->where('staff_role', 'head_coach')->where('is_active', true)->first()?->user;
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (! $this->logo) {
            return null;
        }
        if (Str::startsWith($this->logo, ['http://', 'https://'])) {
            return $this->logo;
        }

        return asset('uploads/teams/'.$this->logo);
    }
}
