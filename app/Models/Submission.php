<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    const PARTICIPANT_TEAM = 'team';

    const PARTICIPANT_INDIVIDUAL = 'individual';

    protected $fillable = [
        'competition_id',
        'participant_type',
        'team_id',
        'user_id',
        'player_id',
        'round_id',
        'title',
        'description',
        'file_path',
        'status',
    ];

    protected $casts = [
        'status' => SubmissionStatus::class,
        'participant_type' => 'string',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(CompetitionRound::class);
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

    public function judgeScores(): HasMany
    {
        return $this->hasMany(JudgeScore::class);
    }

    public function isTeamSubmission(): bool
    {
        return $this->participant_type === self::PARTICIPANT_TEAM;
    }

    public function isIndividualSubmission(): bool
    {
        return $this->participant_type === self::PARTICIPANT_INDIVIDUAL;
    }

    public function getParticipantName(): ?string
    {
        if ($this->isTeamSubmission() && $this->team) {
            return $this->team->name;
        }

        if ($this->isIndividualSubmission()) {
            return $this->user?->name ?? $this->player?->name;
        }

        return null;
    }
}
