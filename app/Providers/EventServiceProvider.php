<?php

namespace App\Providers;

use App\Events\GoalScored;
use App\Events\MatchCompleted;
use App\Listeners\UpdatePlayerStatsAfterGoal;
use App\Listeners\UpdateStandingsAfterMatch;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MatchCompleted::class => [
            UpdateStandingsAfterMatch::class,
        ],
        GoalScored::class => [
            UpdatePlayerStatsAfterGoal::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
