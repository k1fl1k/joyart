<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Likes;

class LikesController extends Controller
{
    public function toggle(Artwork $artwork)
    {
        $userId = Auth::id();

        $like = Likes::where('user_id', $userId)->where('artwork_id', $artwork->id)->first();

        if ($like) {
            $like->delete();
        } else {
            Likes::create([
                'id' => (string) Str::ulid(),
                'user_id' => $userId,
                'artwork_id' => $artwork->id,
                'state' => "like",
            ]);
        }

        return back();
    }
}
