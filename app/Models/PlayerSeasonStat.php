<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerSeasonStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'competition_id',
        'season_year',
        'matches_played',
        'matches_started',
        'minutes_played',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'saves',
        'clean_sheets',
        'tackles',
        'interceptions',
        'key_passes',
        'dribbles',
    ];

    protected $casts = [
        'season_year' => 'integer',
        'matches_played' => 'integer',
        'matches_started' => 'integer',
        'minutes_played' => 'integer',
        'goals' => 'integer',
        'assists' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'saves' => 'integer',
        'clean_sheets' => 'integer',
        'tackles' => 'integer',
        'interceptions' => 'integer',
        'key_passes' => 'integer',
        'dribbles' => 'integer',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }
}
