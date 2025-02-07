<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/v2')->group(function () {
    Route::post('/token', [App\Http\Controllers\Api\Competition\V2\AuthController::class, 'token']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/token', [App\Http\Controllers\Api\Competition\V2\AuthController::class, 'revokeToken']);
        Route::post('/bouts/{bout}/start', [App\Http\Controllers\Api\Competition\V2\BoutController::class, 'start']);
        Route::post('/bouts/{bout}/reset', [App\Http\Controllers\Api\Competition\V2\BoutController::class, 'reset']);
        Route::post('/bouts/{bout}/result', [App\Http\Controllers\Api\Competition\V2\BoutController::class, 'result']);
        Route::resource('bouts', App\Http\Controllers\Api\Competition\V2\BoutController::class);
        Route::get('/contest', [App\Http\Controllers\Api\Competition\V2\ContestController::class, 'get']);
        Route::get('/competition', [App\Http\Controllers\Api\Competition\V2\CompetitionController::class, 'get']);
        Route::get('/weightIn', [App\Http\Controllers\Api\Competition\V2\WeightInController::class, 'get']);
    });
});
