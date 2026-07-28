<?php

namespace App\Providers;

use App\Models\Competition;
use App\Models\Match_;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Policies\CompetitionPolicy;
use App\Policies\MatchPolicy;
use App\Policies\PlayerPolicy;
use App\Policies\TeamPolicy;
use App\Policies\UserPolicy;
use App\Observers\UserObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
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
