<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchEvent extends Model
{
    use HasFactory;

    const EVENT_TYPES = [
        'goal' => 'هدف',
        'own_goal' => 'هدف عكسي',
        'penalty_scored' => 'ركلة جزاء مسجلة',
        'penalty_missed' => 'ركلة جزاء مهدرة',
        'yellow_card' => 'بطاقة صفراء',
        'second_yellow' => 'بطاقة صفراء ثانية',
        'red_card' => 'بطاقة حمراء',
        'substitution_in' => 'تبديل (دخول)',
        'substitution_out' => 'تبديل (خروج)',
        'injury' => 'إصابة',
        'save' => 'تصدي',
        'assist' => 'تمريرة حاسمة',
    ];

    protected $fillable = [
        'match_id',
        'team_id',
        'player_id',
        'event_type',
        'minute',
        'added_time',
        'description',
        'related_player_id',
    ];

    protected $casts = [
        'minute' => 'integer',
        'added_time' => 'integer',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(Match_::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function relatedPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'related_player_id');
    }
}
