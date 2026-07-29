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

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_POSTPONED = 'postponed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ABANDONED = 'abandoned';
    const STATUS_PENDING = 'pending';

    const STATUSES = [
        self::STATUS_SCHEDULED => 'مجدولة',
        self::STATUS_IN_PROGRESS => 'جارية',
        self::STATUS_COMPLETED => 'مكتملة',
        self::STATUS_POSTPONED => 'مؤجلة',
        self::STATUS_CANCELLED => 'ملغاة',
        self::STATUS_ABANDONED => 'ملغية',
        self::STATUS_PENDING => 'قيد الانتظار',
    ];

    const PHASE_SCHEDULED = 'scheduled';
    const PHASE_FIRST_HALF = 'first_half';
    const PHASE_HALF_TIME = 'half_time';
    const PHASE_SECOND_HALF = 'second_half';
    const PHASE_ET_BREAK = 'et_break';
    const PHASE_ET_FIRST_HALF = 'et_first_half';
    const PHASE_ET_HALF_TIME = 'et_half_time';
    const PHASE_ET_SECOND_HALF = 'et_second_half';
    const PHASE_FULL_TIME = 'full_time';

    const PHASES = [
        self::PHASE_SCHEDULED => '—',
        self::PHASE_FIRST_HALF => 'الشوط الأول',
        self::PHASE_HALF_TIME => 'استراحة',
        self::PHASE_SECOND_HALF => 'الشوط الثاني',
        self::PHASE_ET_BREAK => 'استراحة إضافية',
        self::PHASE_ET_FIRST_HALF => 'شوط إضافي أول',
        self::PHASE_ET_HALF_TIME => 'استراحة إضافية',
        self::PHASE_ET_SECOND_HALF => 'شوط إضافي ثاني',
        self::PHASE_FULL_TIME => 'انتهت',
    ];

    protected $fillable = [
        'competition_id',
        'team1_id',
        'team2_id',
        'match_date',
        'score_team1',
        'score_team2',
        'status',
        'venue',
        'weather',
        'attendance',
        'referee',
        'assistant_referee_1',
        'assistant_referee_2',
        'fourth_official',
        'added_time_first_half',
        'added_time_second_half',
        'match_notes',
        'referee_id',
        'assistant_referee_1_id',
        'assistant_referee_2_id',
        'fourth_official_id',
        'round',
        'group_name',
        'stage',
        'leg',
        'bracket',
        'is_bye',
        'is_third_place',
        'extra_data',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'score_team1' => 'integer',
        'score_team2' => 'integer',
        'attendance' => 'integer',
        'added_time_first_half' => 'integer',
        'added_time_second_half' => 'integer',
        'is_bye' => 'boolean',
        'is_third_place' => 'boolean',
        'extra_data' => 'array',
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

    public function referee(): BelongsTo
    {
        return $this->belongsTo(Referee::class, 'referee_id');
    }

    public function assistantReferee1(): BelongsTo
    {
        return $this->belongsTo(Referee::class, 'assistant_referee_1_id');
    }

    public function assistantReferee2(): BelongsTo
    {
        return $this->belongsTo(Referee::class, 'assistant_referee_2_id');
    }

    public function fourthOfficial(): BelongsTo
    {
        return $this->belongsTo(Referee::class, 'fourth_official_id');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(MatchEvent::class, 'match_id')->goal();
    }

    public function lineups(): HasMany
    {
        return $this->hasMany(MatchLineup::class, 'match_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class, 'match_id');
    }

    public function stats(): HasMany
    {
        return $this->hasMany(MatchStat::class, 'match_id');
    }

    public function tactics(): HasMany
    {
        return $this->hasMany(TeamTactic::class, 'match_id');
    }

    public function getStartedAtAttribute(): ?string
    {
        return $this->extra_data['started_at'] ?? null;
    }

    public function getKickoffTimestampAttribute(): int
    {
        if ($this->started_at) {
            return strtotime($this->started_at) * 1000;
        }
        if ($this->match_date) {
            return $this->match_date->timestamp * 1000;
        }
        return 0;
    }

    // ── Phase system ───────────────────────────────────────────────

    public function getPhaseAttribute(): string
    {
        return $this->extra_data['phase'] ?? self::PHASE_SCHEDULED;
    }

    public function setPhase(string $phase): void
    {
        $extra = $this->extra_data ?? [];
        $extra['phase'] = $phase;
        $this->extra_data = $extra;
    }

    public function getFirstHalfStartedAtAttribute(): ?string
    {
        return $this->extra_data['first_half_started_at'] ?? null;
    }

    public function getSecondHalfStartedAtAttribute(): ?string
    {
        return $this->extra_data['second_half_started_at'] ?? null;
    }

    public function getEtFirstHalfStartedAtAttribute(): ?string
    {
        return $this->extra_data['et_first_half_started_at'] ?? null;
    }

    public function getEtSecondHalfStartedAtAttribute(): ?string
    {
        return $this->extra_data['et_second_half_started_at'] ?? null;
    }

    public function getPhaseLabelAttribute(): string
    {
        return __("app.phase_{$this->phase}") ?? self::PHASES[$this->phase] ?? '—';
    }

    public function getAddedTimeEtFirstHalfAttribute(): int
    {
        return (int)($this->extra_data['added_time_et_first_half'] ?? 0);
    }

    public function getAddedTimeEtSecondHalfAttribute(): int
    {
        return (int)($this->extra_data['added_time_et_second_half'] ?? 0);
    }
}
