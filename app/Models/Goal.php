<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'player_id',
        'minute',
    ];

    protected $casts = [
        'minute' => 'integer',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match_::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
