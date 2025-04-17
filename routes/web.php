<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use k1fl1k\joyart\Http\Controllers\AdminController;
use k1fl1k\joyart\Http\Controllers\ArtworkController;
use k1fl1k\joyart\Http\Controllers\CommentsController;
use k1fl1k\joyart\Http\Controllers\FavoritesController;
use k1fl1k\joyart\Http\Controllers\GalleryController;
use k1fl1k\joyart\Http\Controllers\LikesController;
use k1fl1k\joyart\Http\Controllers\ProfileController;
use k1fl1k\joyart\Http\Controllers\WebController;
use k1fl1k\joyart\Models\Tag;

Route::get('/', [WebController::class, 'index'])->name('welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('profile', [WebController::class, 'showProfile'])
    ->middleware(['auth'])
    ->name('profile');
Route::get('/profile/{username}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('settings', [ProfileController::class, 'settings'])
    ->middleware(['auth'])
    ->name('settings.show');
Route::put('/profile/update-description', [ProfileController::class, 'updateDescription'])
    ->middleware(['auth'])
    ->name('profile.updateDescription');
Route::post('/profile/start-editing', [ProfileController::class, 'startEditing'])
    ->middleware(['auth'])
    ->name('profile.startEditing');

Route::get('/tag/{tag:slug}', [GalleryController::class, 'filterByTag'])->name('gallery.byTag');

Route::get('/create', function () {
    return view('create');
})->middleware(['auth'])->name('create.post');
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

Route::get('/artworks/{artwork:slug}', [ArtworkController::class, 'show'])->name('artwork.show');
Route::post('/artworks/{artwork:slug}/likes', [LikesController::class, 'toggle'])->name('likes.toggle')
    ->middleware(['auth']);
Route::post('/artworks/{artwork:slug}/favorites', [FavoritesController::class, 'toggle'])->name('favorites.toggle')
    ->middleware(['auth']);
Route::post('/artworks/{artwork:slug}/comments', [CommentsController::class, 'store'])->name('comments.store')
    ->middleware(['auth']);
Route::delete('/artworks/{artwork:slug}/comments/{comment}', [CommentsController::class, 'destroy'])
    ->name('comments.destroy')
    ->middleware(['auth']);
Route::delete('/artworks/{artwork:slug}', [ArtworkController::class, 'destroy'])->name('artworks.destroy');


Route::get('/artworks/{artwork:slug}/edit', [ArtworkController::class, 'edit'])
    ->middleware(['auth'])
    ->name('artworks.edit');
Route::put('/artworks/{artwork:slug}/edited', [ArtworkController::class, 'update'])
    ->middleware(['auth'])
    ->name('artworks.update');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.panel');
    Route::post('/admin/users', [AdminController::class, 'updateUser'])->name('admin.updateUser');
    Route::post('/admin/fetch', [AdminController::class, 'fetchSafebooru'])->name('admin.fetchSafebooru');
    Route::get('/admin/user-search', [AdminController::class, 'searchUser'])->name('admin.userSearch');
    Route::get('/admin/user-info/{id}', [AdminController::class, 'getUserInfo'])->name('admin.userInfo');

});

require __DIR__.'/auth.php';
