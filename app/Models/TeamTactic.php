<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamTactic extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'pressing_style',
        'build_up_style',
        'defense_style',
        'attack_style',
        'formation_used',
        'match_id',
        'notes',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match_::class);
    }
}
