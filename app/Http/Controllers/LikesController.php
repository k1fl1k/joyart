<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Likes;

class LikesController extends Controller
{
    public function toggle(Request $request, Artwork $artwork)
    {
        $user = Auth::user();
        $liked = $artwork->likes()->where('user_id', $user->id)->exists();

        if ($liked) {
            $artwork->likes()->where('user_id', $user->id)->delete();
        } else {
            $artwork->likes()->create(['id' => (string) Str::ulid(), 'user_id' => $user->id, 'state' => 'like']);
        }

        return response()->json([
            'liked' => !$liked,
            'likes_count' => $artwork->likes()->count(),
        ]);
    }
}
