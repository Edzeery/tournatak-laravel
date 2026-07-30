<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model
{
    use HasFactory, SoftDeletes;

    const FORMAT_LEAGUE = 'league';

    const FORMAT_KNOCKOUT = 'knockout';

    const FORMAT_GROUPS = 'groups';

    const FORMAT_LEAGUE_KNOCKOUT = 'league_knockout';

    const FORMAT_DOUBLE_ELIMINATION = 'double_elimination';

    const FORMAT_SWISS = 'swiss';

    const FORMAT_HOME_AWAY = 'home_away';

    const PROFILE_OFFICIAL = 'official';

    const PROFILE_CASUAL = 'casual';

    const PROFILES = [
        self::PROFILE_OFFICIAL => 'رسمية',
        self::PROFILE_CASUAL => 'غير رسمية',
    ];

    const FORMATS = [
        self::FORMAT_LEAGUE => 'دوري',
        self::FORMAT_KNOCKOUT => 'خروج المغلوب',
        self::FORMAT_GROUPS => 'مجموعات',
        self::FORMAT_LEAGUE_KNOCKOUT => 'دوري + خروج المغلوب',
        self::FORMAT_DOUBLE_ELIMINATION => 'إقصاء مزدوج',
        self::FORMAT_SWISS => 'سويسري',
        self::FORMAT_HOME_AWAY => 'ذهاب وإياب',
    ];

    protected $fillable = [
        'type_id',
        'subtype_id',
        'organizer_id',
        'sport_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'location',
        'approval_status',
        'status',
        'format',
        'format_config',
        'competition_profile',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'format_config' => 'array',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(CompetitionType::class, 'type_id');
    }

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubtype::class, 'subtype_id');
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'registrations')->withPivot('status');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(Match_::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function scopeOfficial($query)
    {
        return $query->where('competition_profile', self::PROFILE_OFFICIAL);
    }

    public function scopeCasual($query)
    {
        return $query->where('competition_profile', self::PROFILE_CASUAL);
    }

    public function isCasual(): bool
    {
        return $this->competition_profile === self::PROFILE_CASUAL;
    }

    public function isOfficial(): bool
    {
        return $this->competition_profile === self::PROFILE_OFFICIAL;
    }
}
