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
    Route::post('/token', 'Api\Contest\V2\AuthController@token');

    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/token', 'Api\Contest\V2\AuthController@revokeToken');
        Route::resource('bouts', 'Api\Contest\V2\BoutController');
        Route::get('/contest', 'Api\Contest\V2\ContestController@get');
        Route::post('/bouts/{bout}/start', 'Api\Contest\V2\BoutController@start');
        Route::post('/bouts/{bout}/reset', 'Api\Contest\V2\BoutController@reset');
        Route::post('/bouts/{bout}/result', 'Api\Contest\V2\BoutController@result');
    });
});
