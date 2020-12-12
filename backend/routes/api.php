<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/companies/{company_id}/contracts', [\App\Models\Back\CompanyContract::class, 'index']);

Route::get('/location-lookup', [\App\Http\Controllers\LocationLookupController::class, 'search']);

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('back')->group(function() {
        Route::get('trainee-groups', [\App\Http\Controllers\Back\TraineesController::class, 'withGroups'])->name('api.back.trainee-groups.index');
    });
    Route::get('/zoom/signature', [\App\Http\Controllers\ZoomController::class, 'signature'])->name('api.zoom.signature');
});
