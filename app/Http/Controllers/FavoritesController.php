<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Favorites;

class FavoritesController extends Controller
{
    public function toggle(Artwork $artwork)
    {
        $userId = Auth::id();

        $favorites = Favorites::where('user_id', $userId)->where('artwork_id', $artwork->id)->first();

        if ($favorites) {
            $favorites->delete();
        } else {
            Favorites::create([
                'id' => (string) Str::ulid(),
                'user_id' => $userId,
                'artwork_id' => $artwork->id,
            ]);
        }

        return back();
    }
}
