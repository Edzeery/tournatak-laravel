<?php

use App\Livewire\Admin\CompetitionDomainsPage;
use App\Livewire\Admin\Competitions\CompetitionJudgingPage;
use App\Livewire\Admin\Competitions\CompetitionsPage;
use App\Livewire\Admin\Competitions\CreateCasualCompetitionPage;
use App\Livewire\Admin\Competitions\CreateCompetitionPage;
use App\Livewire\Admin\Competitions\EditCompetitionPage;
use App\Livewire\Admin\Competitions\RoundsPage;
use App\Livewire\Admin\Competitions\SubmissionsPage;
use App\Livewire\Admin\DashboardPage;
use App\Livewire\Admin\Matches\CreateMatchPage;
use App\Livewire\Admin\Matches\EditMatchPage;
use App\Livewire\Admin\Matches\MatchControlPage;
use App\Livewire\Admin\Matches\MatchesPage;
use App\Livewire\Admin\Matches\MatchEventsPage;
use App\Livewire\Admin\Matches\MatchLineupPage;
use App\Livewire\Admin\Matches\MatchStatsPage;
use App\Livewire\Admin\Players\CreatePlayerPage;
use App\Livewire\Admin\Players\EditPlayerPage;
use App\Livewire\Admin\Players\PlayersPage;
use App\Livewire\Admin\Positions\PositionsPage;
use App\Livewire\Admin\Referees\CreateRefereePage;
use App\Livewire\Admin\Referees\EditRefereePage;
use App\Livewire\Admin\Referees\RefereesPage;
use App\Livewire\Admin\Registrations\CreateRegistrationPage;
use App\Livewire\Admin\Registrations\CreateTeamRegistrationPage;
use App\Livewire\Admin\Registrations\RegistrationsPage;
use App\Livewire\Admin\SecurityLogPage;
use App\Livewire\Admin\Sports\CreateSportPage;
use App\Livewire\Admin\Sports\EditSportPage;
use App\Livewire\Admin\Sports\SportsPage;
use App\Livewire\Admin\Subtypes\CreateSubtypePage;
use App\Livewire\Admin\Subtypes\EditSubtypePage;
use App\Livewire\Admin\Subtypes\SubtypesPage;
use App\Livewire\Admin\Teams\CreateTeamPage;
use App\Livewire\Admin\Teams\EditTeamPage;
use App\Livewire\Admin\Teams\TeamFormationsPage;
use App\Livewire\Admin\Teams\TeamMedicalPage;
use App\Livewire\Admin\Teams\TeamsPage;
use App\Livewire\Admin\Teams\TeamStaffPage;
use App\Livewire\Admin\Teams\TeamStatsPage;
use App\Livewire\Admin\Teams\TeamTacticsPage;
use App\Livewire\Admin\TrashPage;
use App\Livewire\Admin\Types\CreateTypePage;
use App\Livewire\Admin\Types\EditTypePage;
use App\Livewire\Admin\Types\TypesPage;
use App\Livewire\Admin\Users\CreateUserPage;
use App\Livewire\Admin\Users\EditUserPage;
use App\Livewire\Admin\Users\UsersPage;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Auth\TwoFactorChallengePage;
use App\Livewire\Home\HomePage;
use App\Livewire\Judge\JudgingPage as JudgeJudgingPage;
use App\Livewire\Public\CompetitionDetailPage;
use App\Livewire\Public\CompetitionsPage as PublicCompetitionsPage;
use App\Livewire\Public\MatchesPage as PublicMatchesPage;
use App\Livewire\Public\MatchLivePage;
use App\Livewire\Public\PlayerDetailPage;
use App\Livewire\Public\PlayersPage as PublicPlayersPage;
use App\Livewire\Public\TeamDetailPage;
use App\Livewire\Public\TeamsPage as PublicTeamsPage;
use App\Livewire\Security\TwoFactorSetupPage;
use App\Livewire\User\NotificationsPage;
use App\Livewire\User\ProfilePage;
use App\Livewire\User\RegistrationsPage as UserRegistrationsPage;
use App\Livewire\User\SecurityPage;
use App\Livewire\User\UserDashboardPage;
use App\Livewire\User\UserPreferencesPage;
use App\Models\User;
use App\Services\SecurityActivityLogger;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/lang/{locale}', function ($locale) {
    if (! in_array($locale, ['ar', 'en', 'fr', 'es'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    app()->setLocale($locale);

    // Persist to user preference if authenticated
    if (auth()->check() && auth()->user()->preference) {
        auth()->user()->preference->update(['locale' => $locale]);
    }

    return redirect()->back()->withCookie(cookie('locale', $locale, 60 * 24 * 365));
})->name('lang.switch');

Route::get('/', HomePage::class)->name('home');
Route::get('/home', HomePage::class)->name('home.index');

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/competitions', PublicCompetitionsPage::class)->name('competitions.index');
Route::get('/competitions/{competition}', CompetitionDetailPage::class)->name('competitions.show');
Route::get('/teams', PublicTeamsPage::class)->name('teams.index');
Route::get('/teams/{teamId}', TeamDetailPage::class)->name('teams.show');
Route::get('/matches', PublicMatchesPage::class)->name('matches.index');
Route::get('/matches/{match}/live', MatchLivePage::class)->name('matches.live');
Route::get('/players', PublicPlayersPage::class)->name('players.index');
Route::get('/players/{playerId}', PlayerDetailPage::class)->name('players.show');

/*
|--------------------------------------------------------------------------
| Auth Routes (Guest only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class)->name('register');
    Route::get('/forgot-password', ForgotPasswordPage::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPasswordPage::class)->name('password.reset');
    Route::get('/2fa/challenge', TwoFactorChallengePage::class)->name('2fa.challenge');
});

Route::get('/verify-email/{id}/{hash}', function ($id, $hash) {
    $user = User::find($id);

    if (! $user || cache()->pull("email_verification_{$user->id}_{$hash}") === null) {
        abort(403, 'رابط التفعيل غير صالح أو انتهت صلاحيته.');
    }

    $user->update(['is_verified' => true, 'email_verified_at' => now()]);

    SecurityActivityLogger::emailVerified($user);

    return redirect()->route('login')->with('success', 'تم تفعيل حسابك بنجاح! يمكنك الآن تسجيل الدخول.');
})->name('verification.verify');

Route::middleware('auth')->post('/logout', function () {
    SecurityActivityLogger::logout(auth()->user());
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

/*
|--------------------------------------------------------------------------
| User Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', UserDashboardPage::class)->name('dashboard');
    Route::get('/profile', ProfilePage::class)->name('profile');
    Route::get('/notifications', NotificationsPage::class)->name('notifications');
    Route::get('/preferences', UserPreferencesPage::class)->name('preferences');
    Route::get('/security', SecurityPage::class)->name('security');
    Route::get('/security/2fa', TwoFactorSetupPage::class)->name('2fa-setup');
    Route::get('/registrations', UserRegistrationsPage::class)->name('registrations');
});

// Alias route for profile (used in layout)
Route::middleware(['auth'])->get('/profile', function () {
    return redirect()->route('user.profile');
})->name('profile');

/*
|--------------------------------------------------------------------------
| Judge Panel Routes
|--------------------------------------------------------------------------
| Auth only — per-competition access enforced by CompetitionPolicy::judge()
| (assigned judges, owning organizer, or competition managers).
*/
Route::middleware(['auth'])->get('/judge/competitions/{competition}', JudgeJudgingPage::class)->name('judge.competitions.show');

/*
|--------------------------------------------------------------------------
| Admin Routes — Permission-scoped sub-groups
|--------------------------------------------------------------------------
| URL prefix: /panel (visible URL) | Route names: admin.* (unchanged)
| Outer wrapper: auth only. Inner groups: permission: middleware per section.
*/
Route::middleware(['auth'])->prefix('panel')->name('admin.')->group(function () {

    // ── Dashboard (any authenticated with view dashboard) ─────────────
    Route::middleware(['permission:view dashboard'])->group(function () {
        Route::get('/dashboard', DashboardPage::class)->name('dashboard');
    });

    // ── User Management ──────────────────────────────────────────────
    Route::middleware(['permission:manage users'])->group(function () {
        Route::get('/users', UsersPage::class)->name('users.index');
        Route::get('/users/create', CreateUserPage::class)->name('users.create');
        Route::get('/users/{user}/edit', EditUserPage::class)->name('users.edit');
    });

    // ── Competition Management ───────────────────────────────────────
    Route::middleware(['permission:manage competitions'])->group(function () {
        Route::get('/competitions', CompetitionsPage::class)->name('competitions.index');
        Route::get('/competitions/create', CreateCompetitionPage::class)->name('competitions.create');
        Route::get('/competitions/{competition}/edit', EditCompetitionPage::class)->name('competitions.edit');
        Route::get('/competitions/{competition}/rounds', RoundsPage::class)->name('competitions.rounds');
        Route::get('/competitions/{competition}/submissions', SubmissionsPage::class)->name('competitions.submissions');
        Route::get('/competitions/{competition}/judging', CompetitionJudgingPage::class)->name('competitions.judging');
    });

    // ── Registration Management ─────────────────────────────────────
    Route::middleware(['permission:manage competitions'])->group(function () {
        Route::get('/registrations', RegistrationsPage::class)->name('registrations.index');
        Route::get('/registrations/create/individual', CreateRegistrationPage::class)->name('registrations.create.individual');
        Route::get('/registrations/create/team', CreateTeamRegistrationPage::class)->name('registrations.create.team');
    });

    // ── Casual Competition Management (local organizers) ─────────────
    Route::middleware(['permission:manage casual competitions'])->group(function () {
        Route::get('/casual-competitions/create', CreateCasualCompetitionPage::class)->name('competitions.create-casual');
    });

    // ── Competition Types ────────────────────────────────────────────
    Route::middleware(['permission:manage competition types'])->group(function () {
        Route::get('/types', TypesPage::class)->name('types.index');
        Route::get('/types/create', CreateTypePage::class)->name('types.create');
        Route::get('/types/{type}/edit', EditTypePage::class)->name('types.edit');
    });

    // ── Competition Subtypes ─────────────────────────────────────────
    Route::middleware(['permission:manage competition types'])->group(function () {
        Route::get('/subtypes', SubtypesPage::class)->name('subtypes.index');
        Route::get('/subtypes/create', CreateSubtypePage::class)->name('subtypes.create');
        Route::get('/subtypes/{subtype}/edit', EditSubtypePage::class)->name('subtypes.edit');
    });

    // ── Team Management ─────────────────────────────────────────────
    Route::middleware(['permission:manage teams'])->group(function () {
        Route::get('/teams', TeamsPage::class)->name('teams.index');
        Route::get('/teams/create', CreateTeamPage::class)->name('teams.create');
        Route::get('/teams/{team}/edit', EditTeamPage::class)->name('teams.edit');
    });

    // ── Team Sub-pages (granular permissions — coach scoped) ─────────
    Route::middleware(['permission:manage team formations'])->group(function () {
        Route::get('/teams/{team}/formations', TeamFormationsPage::class)->name('teams.formations');
    });
    Route::middleware(['permission:manage team tactics'])->group(function () {
        Route::get('/teams/{team}/tactics', TeamTacticsPage::class)->name('teams.tactics');
    });
    Route::middleware(['permission:manage team medical'])->group(function () {
        Route::get('/teams/{team}/medical', TeamMedicalPage::class)->name('teams.medical');
    });
    Route::middleware(['permission:manage team staff'])->group(function () {
        Route::get('/teams/{team}/staff', TeamStaffPage::class)->name('teams.staff');
    });
    Route::middleware(['permission:manage teams'])->group(function () {
        Route::get('/teams/{team}/stats', TeamStatsPage::class)->name('teams.stats');
    });

    // ── Player Management ────────────────────────────────────────────
    Route::middleware(['permission:manage players'])->group(function () {
        Route::get('/players', PlayersPage::class)->name('players.index');
        Route::get('/players/create', CreatePlayerPage::class)->name('players.create');
        Route::get('/players/{player}/edit', EditPlayerPage::class)->name('players.edit');
    });

    // ── Match Management ─────────────────────────────────────────────
    Route::middleware(['permission:manage matches'])->group(function () {
        Route::get('/matches', MatchesPage::class)->name('matches.index');
        Route::get('/matches/create', CreateMatchPage::class)->name('matches.create');
        Route::get('/matches/{match}/edit', EditMatchPage::class)->name('matches.edit');
        Route::get('/matches/{match}/lineup', MatchLineupPage::class)->name('matches.lineup');
        Route::get('/matches/{match}/events', MatchEventsPage::class)->name('matches.events');
        Route::get('/matches/{match}/stats', MatchStatsPage::class)->name('matches.stats');
        Route::get('/matches/{match}/control', MatchControlPage::class)->name('matches.control');
    });

    // ── Referee Management ──────────────────────────────────────────
    Route::middleware(['permission:manage matches'])->group(function () {
        Route::get('/referees', RefereesPage::class)->name('referees.index');
        Route::get('/referees/create', CreateRefereePage::class)->name('referees.create');
        Route::get('/referees/{referee}/edit', EditRefereePage::class)->name('referees.edit');
    });

    // ── Sports ──────────────────────────────────────────────────────
    Route::middleware(['permission:manage settings'])->group(function () {
        Route::get('/sports', SportsPage::class)->name('sports.index');
        Route::get('/sports/create', CreateSportPage::class)->name('sports.create');
        Route::get('/sports/{sport}/edit', EditSportPage::class)->name('sports.edit');
    });

    // ── Competition Domains ────────────────────────────────────────
    Route::middleware(['permission:manage settings'])->group(function () {
        Route::get('/domains', CompetitionDomainsPage::class)->name('domains.index');
    });

    // ── Positions ────────────────────────────────────────────────────
    Route::middleware(['permission:manage settings'])->group(function () {
        Route::get('/positions', PositionsPage::class)->name('positions.index');
    });

    // ── Trash (admin only) ───────────────────────────────────────────
    Route::middleware(['permission:manage admin users'])->group(function () {
        Route::get('/trash', TrashPage::class)->name('trash');
    });

    // ── Security Log (admin only) ────────────────────────────────────
    Route::middleware(['permission:manage admin users'])->group(function () {
        Route::get('/security-log', SecurityLogPage::class)->name('security-log');
    });
});
