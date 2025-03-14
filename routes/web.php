<?php

use Illuminate\Support\Facades\Route;
use k1fl1k\joyart\Http\Controllers\WebController;

Route::get('/', [WebController::class, 'showWelcome'])->name('welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('profile', [WebController::class, 'showProfile'])
    ->middleware(['auth'])
    ->name('profile');

Route::view('/profile/settings', "settings")
    ->middleware(['auth'])
    ->name('settings');

require __DIR__.'/auth.php';
