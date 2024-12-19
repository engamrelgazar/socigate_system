<?php

use App\Http\Controllers\authController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/password-reset/{token}', [authController::class, 'showResetForm'])->name('password.reset');
Route::post('/password-reset', [authController::class, 'resetPassword'])->name('password.update');
Route::get('/password-reset-success', function () {
    return view('auth.passwords.success');
})->name('password.success');
