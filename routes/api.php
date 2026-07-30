<?php

use App\Http\Controllers\Api\CompetitionController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    $dbStatus = 'healthy';
    try {
        DB::connection()->getPdo();
    } catch (Throwable) {
        $dbStatus = 'unhealthy';
    }

    $cacheStatus = 'healthy';
    try {
        Cache::store('array')->has('health-check');
    } catch (Throwable) {
        $cacheStatus = 'unhealthy';
    }

    return response()->json([
        'status' => $dbStatus === 'healthy' ? 'healthy' : 'degraded',
        'timestamp' => now()->toIso8601String(),
        'services' => [
            'database' => $dbStatus,
            'cache' => $cacheStatus,
        ],
        'app' => [
            'env' => config('app.env'),
            'debug' => config('app.debug'),
        ],
    ]);
})->name('api.health');

Route::post('/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required|string|max:255',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
    }

    $abilities = $request->abilities ?? ['read'];
    $token = $user->createToken($request->device_name, $abilities);

    return response()->json(['token' => $token->plainTextToken]);
})->middleware('throttle:20,1')->name('api.token');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('api.home');
    Route::get('/competitions', [CompetitionController::class, 'index'])->name('api.competitions.index');
    Route::get('/teams', [TeamController::class, 'index'])->name('api.teams.index');
    Route::get('/players', [PlayerController::class, 'index'])->name('api.players.index');
    Route::get('/matches', [MatchController::class, 'index'])->name('api.matches.index');

    Route::get('/user', fn (Request $r) => $r->user()->load('roles', 'permissions'))->name('api.user');
});
