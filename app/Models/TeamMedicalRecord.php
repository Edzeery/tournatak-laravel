<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'player_id',
        'record_type',
        'injury_name',
        'severity',
        'status',
        'injury_date',
        'expected_return',
        'actual_return',
        'treatment',
        'notes',
        'reported_by',
    ];

    protected $casts = [
        'injury_date' => 'date',
        'expected_return' => 'date',
        'actual_return' => 'date',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function getDaysOutAttribute(): ?int
    {
        if (! $this->injury_date) {
            return null;
        }

        return Carbon::parse($this->injury_date)->diffInDays(now());
    }
}
