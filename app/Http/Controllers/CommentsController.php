<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Comments;

class CommentsController extends Controller
{
    public function store(Request $request, Artwork $artwork)
    {
        $request->validate([
            'body' => 'required',
        ]);

        Comments::create([
            'id' => (string) Str::ulid(),
            'user_id' => Auth::id(),
            'artwork_id' => $artwork->id,
            'body' => $request->body,
        ]);

        return back();
    }
}
