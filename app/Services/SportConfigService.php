<?php

namespace App\Services;

use App\Models\Position;
use App\Models\Sport;
use Illuminate\Support\Collection;

class SportConfigService
{
    /**
     * Get position categories for a sport, with fallback to defaults.
     */
    public function getPositionCategories(?Sport $sport): array
    {
        return $sport?->position_categories ?? Sport::DEFAULT_POSITION_CATEGORIES;
    }

    /**
     * Get match event types for a sport, with fallback to defaults.
     */
    public function getMatchEventTypes(?Sport $sport): array
    {
        return $sport?->match_event_types ?? Sport::DEFAULT_MATCH_EVENT_TYPES;
    }

    /**
     * Get positions for a sport, falling back to type-based lookup.
     */
    public function getPositions(?Sport $sport, ?string $sportType = null): Collection
    {
        return Position::cachedActive(sportId: $sport?->id, sportType: $sportType ?? 'football');
    }

    /**
     * Check if a category is valid for a given sport.
     */
    public function isValidCategory(?Sport $sport, string $category): bool
    {
        return in_array($category, $this->getPositionCategories($sport), true);
    }

    /**
     * Check if an event type is valid for a given sport.
     */
    public function isValidEventType(?Sport $sport, string $eventType): bool
    {
        return in_array($eventType, $this->getMatchEventTypes($sport), true);
    }
}
