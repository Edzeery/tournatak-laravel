<?php

use App\Livewire\Admin\Competitions\CompetitionsPage;
use App\Livewire\Admin\Competitions\CreateCompetitionPage;
use App\Livewire\Admin\Competitions\EditCompetitionPage;
use App\Livewire\Admin\DashboardPage;
use App\Livewire\Admin\Users\CreateUserPage;
use App\Livewire\Admin\Users\EditUserPage;
use App\Livewire\Admin\Users\UsersPage;
use App\Livewire\Admin\Teams\TeamsPage;
use App\Livewire\Admin\Teams\CreateTeamPage;
use App\Livewire\Admin\Teams\EditTeamPage;
use App\Livewire\Admin\Players\PlayersPage;
use App\Livewire\Admin\Players\CreatePlayerPage;
use App\Livewire\Admin\Players\EditPlayerPage;
use App\Livewire\Admin\Types\TypesPage;
use App\Livewire\Admin\Types\CreateTypePage;
use App\Livewire\Admin\Types\EditTypePage;
use App\Livewire\Admin\Subtypes\SubtypesPage;
use App\Livewire\Admin\Subtypes\CreateSubtypePage;
use App\Livewire\Admin\Subtypes\EditSubtypePage;
use App\Livewire\Admin\Matches\MatchesPage;
use App\Livewire\Admin\Matches\CreateMatchPage;
use App\Livewire\Admin\Matches\EditMatchPage;
use App\Livewire\Admin\Teams\TeamStaffPage;
use App\Livewire\Admin\Teams\TeamFormationsPage;
use App\Livewire\Admin\Teams\TeamTacticsPage;
use App\Livewire\Admin\Teams\TeamMedicalPage;
use App\Livewire\Admin\Teams\TeamStatsPage;
use App\Livewire\Admin\Matches\MatchLineupPage;
use App\Livewire\Admin\Matches\MatchEventsPage;
use App\Livewire\Admin\Matches\MatchStatsPage;
use App\Livewire\Admin\Positions\PositionsPage;
use App\Livewire\Public\TeamDetailPage;
use App\Livewire\Public\PlayerDetailPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Auth\TwoFactorChallengePage;
use App\Livewire\Home\HomePage;
use App\Livewire\Public\CompetitionsPage as PublicCompetitionsPage;
use App\Livewire\Public\TeamsPage as PublicTeamsPage;
use App\Livewire\Public\PlayersPage as PublicPlayersPage;
use App\Livewire\User\ProfilePage;
use App\Livewire\User\UserDashboardPage;
use App\Livewire\User\SecurityPage;
use App\Livewire\Security\TwoFactorSetupPage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['ar', 'en', 'fr', 'es'])) {
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
Route::get('/teams', PublicTeamsPage::class)->name('teams.index');
Route::get('/teams/{teamId}', TeamDetailPage::class)->name('teams.show');
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
    $user = \App\Models\User::find($id);

    if (!$user || cache()->pull("email_verification_{$user->id}_{$hash}") === null) {
        abort(403, 'رابط التفعيل غير صالح أو انتهت صلاحيته.');
    }

    $user->update(['is_verified' => true, 'email_verified_at' => now()]);

    \App\Services\SecurityActivityLogger::emailVerified($user);

    return redirect()->route('login')->with('success', 'تم تفعيل حسابك بنجاح! يمكنك الآن تسجيل الدخول.');
})->name('verification.verify');

Route::middleware('auth')->post('/logout', function () {
    \App\Services\SecurityActivityLogger::logout(auth()->user());
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
    Route::get('/security', SecurityPage::class)->name('security');
    Route::get('/security/2fa', TwoFactorSetupPage::class)->name('2fa-setup');
});

// Alias route for profile (used in layout)
Route::middleware(['auth'])->get('/profile', function () {
    return redirect()->route('user.profile');
})->name('profile');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardPage::class)->name('dashboard');

    // Users
    Route::get('/users', UsersPage::class)->name('users.index');
    Route::get('/users/create', CreateUserPage::class)->name('users.create');
    Route::get('/users/{user}/edit', EditUserPage::class)->name('users.edit');

    // Competitions
    Route::get('/competitions', CompetitionsPage::class)->name('competitions.index');
    Route::get('/competitions/create', CreateCompetitionPage::class)->name('competitions.create');
    Route::get('/competitions/{competition}/edit', EditCompetitionPage::class)->name('competitions.edit');

    // Teams
    Route::get('/teams', TeamsPage::class)->name('teams.index');
    Route::get('/teams/create', CreateTeamPage::class)->name('teams.create');
    Route::get('/teams/{team}/edit', EditTeamPage::class)->name('teams.edit');

    // Players
    Route::get('/players', PlayersPage::class)->name('players.index');
    Route::get('/players/create', CreatePlayerPage::class)->name('players.create');
    Route::get('/players/{player}/edit', EditPlayerPage::class)->name('players.edit');

    // Competition Types
    Route::get('/types', TypesPage::class)->name('types.index');
    Route::get('/types/create', CreateTypePage::class)->name('types.create');
    Route::get('/types/{type}/edit', EditTypePage::class)->name('types.edit');

    // Competition Subtypes
    Route::get('/subtypes', SubtypesPage::class)->name('subtypes.index');
    Route::get('/subtypes/create', CreateSubtypePage::class)->name('subtypes.create');
    Route::get('/subtypes/{subtype}/edit', EditSubtypePage::class)->name('subtypes.edit');

    // Matches
    Route::get('/matches', MatchesPage::class)->name('matches.index');
    Route::get('/matches/create', CreateMatchPage::class)->name('matches.create');
    Route::get('/matches/{match}/edit', EditMatchPage::class)->name('matches.edit');

    // Team sub-pages
    Route::get('/teams/{team}/staff', TeamStaffPage::class)->name('teams.staff');
    Route::get('/teams/{team}/formations', TeamFormationsPage::class)->name('teams.formations');
    Route::get('/teams/{team}/tactics', TeamTacticsPage::class)->name('teams.tactics');
    Route::get('/teams/{team}/medical', TeamMedicalPage::class)->name('teams.medical');
    Route::get('/teams/{team}/stats', TeamStatsPage::class)->name('teams.stats');

    // Match sub-pages
    Route::get('/matches/{match}/lineup', MatchLineupPage::class)->name('matches.lineup');
    Route::get('/matches/{match}/events', MatchEventsPage::class)->name('matches.events');
    Route::get('/matches/{match}/stats', MatchStatsPage::class)->name('matches.stats');

    // Positions
    Route::get('/positions', PositionsPage::class)->name('positions.index');

    // Trash
    Route::get('/trash', \App\Livewire\Admin\TrashPage::class)->name('trash');

    // Security Log
    Route::get('/security-log', \App\Livewire\Admin\SecurityLogPage::class)->name('security-log');
});
