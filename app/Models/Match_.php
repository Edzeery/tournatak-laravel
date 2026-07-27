<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Match_ extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'matches';

    protected $fillable = [
        'competition_id',
        'team1_id',
        'team2_id',
        'match_date',
        'score_team1',
        'score_team2',
        'status',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'score_team1' => 'integer',
        'score_team2' => 'integer',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(MatchLineup::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(MatchStat::class);
    }

    public function tactics(): HasMany
    {
        return $this->hasMany(TeamTactic::class);
    }
}
