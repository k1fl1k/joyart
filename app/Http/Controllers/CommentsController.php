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

    public function destroy(Artwork $artwork, Comments $comment)
    {
        // Перевіряємо, чи коментар належить вказаній роботі
        if ($comment->artwork_id !== $artwork->id) {
            return back()->with('error', 'Коментар не належить цій роботі.');
        }

        // Видалення доступне автору коментаря або адміну
        if ($comment->user_id === Auth::id() || Auth::user()->isAdmin()) {
            $comment->delete();
            return back()->with('success', 'Коментар видалено.');
        }

        return back()->with('error', 'Ви не маєте прав для видалення цього коментаря.');
    }

}
