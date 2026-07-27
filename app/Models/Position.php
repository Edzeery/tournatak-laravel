<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'category',
        'sport_type',
        'abbreviation',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function matchLineups(): HasMany
    {
        return $this->hasMany(MatchLineup::class);
    }

    public static function cachedActive(?string $sportType = null): \Illuminate\Support\Collection
    {
        $key = 'positions_active_' . ($sportType ?? 'all');

        return Cache::tags(['positions'])->remember($key, 3600, function () use ($sportType) {
            $query = static::where('is_active', true)->orderBy('sort_order');
            if ($sportType) {
                $query->where('sport_type', $sportType);
            }
            return $query->get();
        });
    }

    public static function bustCache(): void
    {
        Cache::tags(['positions'])->flush();
    }
}
