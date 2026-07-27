<?php

use App\Http\Controllers\Api\CompetitionController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MatchController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [HomeController::class, 'index'])->name('api.home');
Route::get('/competitions', [CompetitionController::class, 'index'])->name('api.competitions.index');
Route::get('/teams', [TeamController::class, 'index'])->name('api.teams.index');
Route::get('/players', [PlayerController::class, 'index'])->name('api.players.index');
Route::get('/matches', [MatchController::class, 'index'])->name('api.matches.index');
