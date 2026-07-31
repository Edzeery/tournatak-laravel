<?php

namespace App\Providers;

use App\Models\Competition;
use App\Models\Judge;
use App\Models\JudgeScore;
use App\Models\Match_;
use App\Models\Player;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use App\Observers\UserObserver;
use App\Policies\CompetitionPolicy;
use App\Policies\JudgePolicy;
use App\Policies\JudgeScorePolicy;
use App\Policies\MatchPolicy;
use App\Policies\PlayerPolicy;
use App\Policies\RegistrationPolicy;
use App\Policies\SubmissionPolicy;
use App\Policies\TeamPolicy;
use App\Policies\UserPolicy;
use App\Services\ScoringEngineRegistry;
use App\Services\SportsScoringEngine;
use App\Services\SubmissionScoringEngine;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ScoringEngineRegistry::class, function ($app) {
            return new ScoringEngineRegistry(
                $app->make(SportsScoringEngine::class),
                $app->make(SubmissionScoringEngine::class),
            );
        });
    }

    public function boot(): void
    {
        User::observe(UserObserver::class);
        Paginator::defaultView('vendor.pagination.custom');

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Match_::class, MatchPolicy::class);
        Gate::policy(Competition::class, CompetitionPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(Player::class, PlayerPolicy::class);
        Gate::policy(Registration::class, RegistrationPolicy::class);
        Gate::policy(Judge::class, JudgePolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(JudgeScore::class, JudgeScorePolicy::class);

        RateLimiter::for('login', function () {
            return Limit::perMinute(5)->by(request()->ip());
        });

        RateLimiter::for('2fa', function () {
            return Limit::perMinute(5)->by(request()->ip());
        });

        RateLimiter::for('password-reset', function ($request) {
            return Limit::perMinute(3)->by($request->input('email'));
        });
    }
}
