<?php

use App\Http\Controllers\Api\V1\LeaderboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/players', [LeaderboardController::class, 'store']);
    Route::post('/players/{player}/score', [LeaderboardController::class, 'updateScore']);
    Route::get('/leaderboard', [LeaderboardController::class, 'top']);
    Route::get('/players/{player}/rank', [LeaderboardController::class, 'rank']);
});
