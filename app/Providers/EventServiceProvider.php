<?php

namespace App\Providers;

use App\Events\CompetitionStatusChanged;
use App\Events\GoalScored;
use App\Events\JudgeAssigned;
use App\Events\MatchCompleted;
use App\Events\MatchScheduled;
use App\Events\MatchStarted;
use App\Events\PlayerTeamAssigned;
use App\Events\RegistrationStatusChanged;
use App\Events\SubmissionStatusChanged;
use App\Events\TeamStaffAssigned;
use App\Listeners\NotifyCompetitionStatusChanged;
use App\Listeners\NotifyJudgeAssigned;
use App\Listeners\NotifyMatchParticipants;
use App\Listeners\NotifyPlayerTeamAssigned;
use App\Listeners\NotifyRegistrationStatusChanged;
use App\Listeners\NotifySubmissionStatusChanged;
use App\Listeners\NotifyTeamStaffAssigned;
use App\Listeners\UpdatePlayerStatsAfterGoal;
use App\Listeners\UpdateStandingsAfterMatch;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        MatchCompleted::class => [
            UpdateStandingsAfterMatch::class,
            NotifyMatchParticipants::class.'@handleMatchCompleted',
        ],
        GoalScored::class => [
            UpdatePlayerStatsAfterGoal::class,
            NotifyMatchParticipants::class.'@handleGoalScored',
        ],
        MatchStarted::class => [
            NotifyMatchParticipants::class.'@handleMatchStarted',
        ],
        MatchScheduled::class => [
            NotifyMatchParticipants::class.'@handleMatchScheduled',
        ],
        RegistrationStatusChanged::class => [
            NotifyRegistrationStatusChanged::class,
        ],
        CompetitionStatusChanged::class => [
            NotifyCompetitionStatusChanged::class,
        ],
        JudgeAssigned::class => [
            NotifyJudgeAssigned::class,
        ],
        SubmissionStatusChanged::class => [
            NotifySubmissionStatusChanged::class,
        ],
        TeamStaffAssigned::class => [
            NotifyTeamStaffAssigned::class,
        ],
        PlayerTeamAssigned::class => [
            NotifyPlayerTeamAssigned::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
