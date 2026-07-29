<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Player extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'team_id',
        'number',
        'position_text',
        'image',
        'position_id',
        'date_of_birth',
        'nationality',
        'height',
        'weight',
        'foot',
        'sport_type',
        'bio',
        'is_captain',
    ];

    protected $casts = [
        'number' => 'integer',
        'date_of_birth' => 'date',
        'is_captain' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(MatchEvent::class)->goal();
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(TeamMedicalRecord::class);
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(MatchLineup::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class);
    }

    public function seasonStats(): HasMany
    {
        return $this->hasMany(PlayerSeasonStat::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->user->name ?? null;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }
        return asset('uploads/players/' . $this->image);
    }
}
