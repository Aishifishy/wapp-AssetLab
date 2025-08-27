<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReservationConflictController;
use App\Http\Controllers\Admin\EquipmentController;

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

// Equipment API routes (protected by web middleware for CSRF)
Route::middleware('web')->group(function () {
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('api.equipment.show');
});

// Reservation conflict checking endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reservation/check-conflict', [ReservationConflictController::class, 'checkConflict']);
    Route::post('/reservation/check-recurring-conflicts', [ReservationConflictController::class, 'checkRecurringConflicts']);
});

// Public endpoints for AJAX requests (protected by CSRF)
Route::middleware('web')->group(function () {
    Route::post('/reservation/check-conflict', [ReservationConflictController::class, 'checkConflict']);
    Route::post('/reservation/check-recurring-conflicts', [ReservationConflictController::class, 'checkRecurringConflicts']);
});