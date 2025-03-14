<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Tag;

class WebController extends Controller
{
    public function showWelcome()
    {
        $tags = Tag::whereNull('parent_id')->with('subtags')->get();
        $images = Artwork::where('is_published', true)->paginate(50);

        // Повертаємо вигляд і передаємо дані
        return view('welcome', compact('tags', 'images'));

    }

    public function showProfile()
    {
        $user = Auth::user();

        // Пости користувача
        $userPosts = Artwork::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Лайкнуті пости
        $likedPosts = Artwork::whereHas('favorites', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('created_at', 'desc')->get();

        return view('profile', compact('user', 'userPosts', 'likedPosts'));
    }

    public function showSettings()
    {
        return view('settings');
    }
}
