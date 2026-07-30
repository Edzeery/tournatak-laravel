<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends Model
{
    use HasFactory;

    const CATEGORY_TEAM = 'team';

    const CATEGORY_INDIVIDUAL = 'individual';

    const DEFAULT_POSITION_CATEGORIES = ['goalkeeper', 'defender', 'midfielder', 'forward'];

    const DEFAULT_MATCH_EVENT_TYPES = ['goal', 'own_goal', 'penalty_scored', 'assist', 'yellow_card', 'second_yellow', 'red_card', 'substitution_in', 'substitution_out'];

    protected $fillable = [
        'name',
        'name_en',
        'name_fr',
        'name_es',
        'slug',
        'category',
        'icon',
        'position_categories',
        'match_event_types',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'position_categories' => 'array',
        'match_event_types' => 'array',
    ];

    /**
     * Get position categories, falling back to defaults if not set.
     */
    public function getPositionCategoriesAttribute($value): array
    {
        return $value ?: self::DEFAULT_POSITION_CATEGORIES;
    }

    /**
     * Get match event types, falling back to defaults if not set.
     */
    public function getMatchEventTypesAttribute($value): array
    {
        return $value ?: self::DEFAULT_MATCH_EVENT_TYPES;
    }

    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function localizedName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $localized = "name_{$locale}";

        return $this->$localized ?? $this->name;
    }
}
