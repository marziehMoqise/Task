<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::middleware(['auth:sanctum', 'can:manage-drivers'])->group(function () {
//    Route::post('/driver/disable', [DriverController::class, 'disableDriver']);
//    Route::post('/driver/enable', [DriverController::class, 'enableDriver']);
//});

Route::get('/search', [SearchController::class, 'search']);

Route::post('/driver/disable', [DriverController::class, 'disableDriver']);
Route::post('/driver/enable', [DriverController::class, 'enableDriver']);

