<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchLineup extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'player_id',
        'team_id',
        'position_id',
        'is_starter',
        'jersey_number',
        'minute_in',
        'minute_out',
        'sub_reason',
        'is_captain',
        'performance_notes',
    ];

    protected $casts = [
        'is_starter' => 'boolean',
        'jersey_number' => 'integer',
        'minute_in' => 'integer',
        'minute_out' => 'integer',
        'is_captain' => 'boolean',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match_::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }
}
