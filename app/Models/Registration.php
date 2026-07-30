<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    use HasFactory;

    const PARTICIPANT_TEAM = 'team';

    const PARTICIPANT_INDIVIDUAL = 'individual';

    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'competition_id',
        'participant_type',
        'team_id',
        'user_id',
        'player_id',
        'status',
    ];

    protected $casts = [
        'participant_type' => 'string',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function isTeamRegistration(): bool
    {
        return $this->participant_type === self::PARTICIPANT_TEAM;
    }

    public function isIndividualRegistration(): bool
    {
        return $this->participant_type === self::PARTICIPANT_INDIVIDUAL;
    }

    public function getParticipantName(): ?string
    {
        if ($this->isTeamRegistration() && $this->team) {
            return $this->team->name;
        }

        if ($this->isIndividualRegistration()) {
            return $this->user?->name ?? $this->player?->name;
        }

        return null;
    }
}
