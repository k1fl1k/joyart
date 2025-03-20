<?php

use Illuminate\Support\Facades\Route;
use k1fl1k\joyart\Http\Controllers\ArtworkController;
use k1fl1k\joyart\Http\Controllers\GalleryController;
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

Route::get('/tag/{tag:slug}', [GalleryController::class, 'filterByTag'])->name('gallery.byTag');

Route::get('/artwork/{artwork:slug}', [ArtworkController::class, 'show'])->name('artwork.show');

require __DIR__.'/auth.php';
