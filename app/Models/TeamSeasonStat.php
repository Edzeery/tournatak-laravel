<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamSeasonStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'competition_id',
        'season_year',
        'matches_played',
        'wins',
        'draws',
        'losses',
        'goals_for',
        'goals_against',
        'clean_sheets',
        'points',
        'yellow_cards',
        'red_cards',
        'possession_avg',
        'shots_per_match',
    ];

    protected $casts = [
        'season_year' => 'integer',
        'matches_played' => 'integer',
        'wins' => 'integer',
        'draws' => 'integer',
        'losses' => 'integer',
        'goals_for' => 'integer',
        'goals_against' => 'integer',
        'clean_sheets' => 'integer',
        'points' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'possession_avg' => 'integer',
        'shots_per_match' => 'integer',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function getGoalDifferenceAttribute(): int
    {
        return $this->goals_for - $this->goals_against;
    }
}
