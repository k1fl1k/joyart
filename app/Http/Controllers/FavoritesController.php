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
        $user = Auth::user();
        $favorited = $artwork->favorites()->where('user_id', $user->id)->exists();

        if ($favorited) {
            $artwork->favorites()->where('user_id', $user->id)->delete();
        } else {
            $artwork->favorites()->create(['id' => (string) Str::ulid(), 'user_id' => $user->id]);
        }

        return response()->json([
            'favorited' => !$favorited,
        ]);
    }
}
