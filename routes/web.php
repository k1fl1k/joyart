<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use k1fl1k\joyart\Http\Controllers\ArtworkController;
use k1fl1k\joyart\Http\Controllers\CommentsController;
use k1fl1k\joyart\Http\Controllers\FavoritesController;
use k1fl1k\joyart\Http\Controllers\GalleryController;
use k1fl1k\joyart\Http\Controllers\LikesController;
use k1fl1k\joyart\Http\Controllers\ProfileController;
use k1fl1k\joyart\Http\Controllers\WebController;
use k1fl1k\joyart\Models\Tag;

Route::get('/', [WebController::class, 'showWelcome'])->name('welcome');
Route::get('/', [GalleryController::class, 'search'])->name('welcome');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('profile', [WebController::class, 'showProfile'])
    ->middleware(['auth'])
    ->name('profile');
Route::view('/profile/settings', 'settings')
    ->middleware(['auth'])
    ->name('settings');
Route::put('/profile/update-description', [ProfileController::class, 'updateDescription'])
    ->middleware(['auth'])
    ->name('profile.updateDescription');
Route::post('/profile/start-editing', [ProfileController::class, 'startEditing'])
    ->middleware(['auth'])
    ->name('profile.startEditing');

Route::get('/tag/{tag:slug}', [GalleryController::class, 'filterByTag'])->name('gallery.byTag');

Route::get('/create', function () {
    return view('create');
})->name('create.post')->middleware(['auth']);;
Route::get('/tags/search', function (Request $request) {
    $query = $request->query('query');

    if (!$query) {
        return response()->json([]);
    }

    $tags = Tag::where('name', 'like', "%{$query}%")
        ->orderBy('name')
        ->limit(10)
        ->get(['name']);

    return response()->json($tags);
});
Route::post('/artworks', [ArtworkController::class, 'store'])->name('artworks.store');

Route::get('/artwork/{artwork:slug}', [ArtworkController::class, 'show'])->name('artwork.show');
Route::post('/artworks/{artwork:slug}/likes', [LikesController::class, 'toggle'])->name('likes.toggle')
    ->middleware(['auth']);
Route::post('/artworks/{artwork:slug}/favorites', [FavoritesController::class, 'toggle'])->name('favorites.toggle')
    ->middleware(['auth']);
Route::post('/artworks/{artwork:slug}/comments', [CommentsController::class, 'store'])->name('comments.store')
    ->middleware(['auth']);
Route::delete('/artworks/{artwork:slug}/comments/{comment}', [CommentsController::class, 'destroy'])
    ->name('comments.destroy')
    ->middleware(['auth']);

require __DIR__.'/auth.php';
