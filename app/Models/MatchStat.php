<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'team_id',
        'possession',
        'shots_total',
        'shots_on_target',
        'shots_off_target',
        'corners',
        'fouls',
        'offsides',
        'yellow_cards',
        'red_cards',
        'passes_total',
        'passes_accurate',
        'tackles',
        'saves',
        'hit_woodwork',
        'blocked_shots',
    ];

    protected $casts = [
        'possession' => 'decimal:1',
        'shots_total' => 'integer',
        'shots_on_target' => 'integer',
        'shots_off_target' => 'integer',
        'corners' => 'integer',
        'fouls' => 'integer',
        'offsides' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'passes_total' => 'integer',
        'passes_accurate' => 'integer',
        'tackles' => 'integer',
        'saves' => 'integer',
        'hit_woodwork' => 'integer',
        'blocked_shots' => 'integer',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match_::class, 'match_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
