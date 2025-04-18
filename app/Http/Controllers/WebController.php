<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Tag;
use Illuminate\Http\Request;

class WebController extends Controller
{
    public function index(Request $request)
    {
        $query = Artwork::where('is_published', true);

        // Гість не бачить "questionable"
        if (!auth()->check()) {
            $query->where('rating', '!=', 'questionable');
        }

        if ($request->has('search') && $request->search) {
            $query->whereHas('tags', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Фільтрація
        $filter = $request->input('filter');
        if ($filter === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($filter === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($filter === 'image') {
            $query->where('type', 'image');
        } elseif ($filter === 'video') {
            $query->where('type', 'video');
        }

        $tags = Tag::whereNull('parent_id')->with('subtags')->get();

        $images = $query->paginate(50);
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
}
