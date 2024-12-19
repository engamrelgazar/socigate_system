<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\authController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/migrate-and-seed', [DatabaseController::class, 'migrateAndSeed']);
Route::post('/password/forgot', [authController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [authController::class, 'resetPassword']);
Route::post('/login', [authController::class, 'login']);
Route::middleware([\App\Http\Middleware\CheckApiKey::class])->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::get('user/get-user-data', [UserController::class, 'showByApiKey']);
    Route::apiResource('employee', EmployeeController::class);
    Route::apiResource('activity_logs', ActivityLogController::class);
});
