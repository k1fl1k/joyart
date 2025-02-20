<?php

use Illuminate\Support\Facades\Route;
use k1fl1k\joyart\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'showWelcome'])->name('welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
