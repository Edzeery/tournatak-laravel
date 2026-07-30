<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetitionType extends Model
{
    use HasFactory;

    const PARTICIPANT_TEAM = 'team';

    const PARTICIPANT_INDIVIDUAL = 'individual';

    const PARTICIPANT_BOTH = 'both';

    protected $table = 'competition_types';

    protected $fillable = [
        'subtype_id',
        'name',
        'slug',
        'description',
        'icon',
        'participant_type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'participant_type' => 'string',
    ];

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(CompetitionSubtype::class, 'subtype_id');
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class, 'type_id');
    }

    public function supportsTeams(): bool
    {
        return in_array($this->participant_type, [self::PARTICIPANT_TEAM, self::PARTICIPANT_BOTH]);
    }

    public function supportsIndividuals(): bool
    {
        return in_array($this->participant_type, [self::PARTICIPANT_INDIVIDUAL, self::PARTICIPANT_BOTH]);
    }
}
