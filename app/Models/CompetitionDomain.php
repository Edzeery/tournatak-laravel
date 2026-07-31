<?php

namespace App\Models;

use App\Enums\CompetitionEvaluationBasis;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionDomain extends Model
{
    use HasFactory;

    const SLUG_SPORTS = 'sports';

    const SLUG_ESPORTS = 'esports';

    const SLUG_ACADEMIC = 'academic';

    const SLUG_HACKATHON = 'hackathon';

    const SLUG_CREATIVE = 'creative';

    const SLUGS = [
        self::SLUG_SPORTS,
        self::SLUG_ESPORTS,
        self::SLUG_ACADEMIC,
        self::SLUG_HACKATHON,
        self::SLUG_CREATIVE,
    ];

    const PARTICIPANT_TEAM = 'team';

    const PARTICIPANT_INDIVIDUAL = 'individual';

    const PARTICIPANT_BOTH = 'both';

    protected $fillable = [
        'name',
        'name_en',
        'name_fr',
        'name_es',
        'slug',
        'icon',
        'description',
        'evaluation_basis',
        'participant_basis',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'evaluation_basis' => CompetitionEvaluationBasis::class,
    ];

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    public function isSports(): bool
    {
        return $this->slug === self::SLUG_SPORTS;
    }

    public function usesMatchEvaluation(): bool
    {
        return $this->evaluation_basis === CompetitionEvaluationBasis::Match;
    }

    public function usesSubmissionEvaluation(): bool
    {
        return $this->evaluation_basis === CompetitionEvaluationBasis::Submission;
    }

    public function supportsTeams(): bool
    {
        return in_array($this->participant_basis, [self::PARTICIPANT_TEAM, self::PARTICIPANT_BOTH]);
    }

    public function supportsIndividuals(): bool
    {
        return in_array($this->participant_basis, [self::PARTICIPANT_INDIVIDUAL, self::PARTICIPANT_BOTH]);
    }

    public function localizedName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $localized = "name_{$locale}";

        return $this->$localized ?? $this->name;
    }
}
