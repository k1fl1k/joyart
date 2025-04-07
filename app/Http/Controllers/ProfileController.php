<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\User;

class ProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $userPosts = $user->artworks;
        $likedPosts = Artwork::whereHas('favorites', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('created_at', 'desc')->get();

        return view('profile', compact('user', 'userPosts', 'likedPosts'));
    }

    public function startEditing(Request $request)
    {
        // Встановлюємо сесію для редагування опису
        $request->session()->put('editing_description', true);

        return redirect()->back();
    }

    public function updateDescription(Request $request)
    {
        // Валідація опису
        $request->validate([
            'description' => 'nullable|string',
        ]);

        // Оновлення опису користувача
        $user = Auth::user();
        $user->description = $request->description;
        $user->save();

        // Видалити сесію редагування після збереження
        $request->session()->forget('editing_description');

        return redirect()->back()->with('success', 'Description updated successfully.');
    }

    public function settings()
    {
        return view('settings');
    }
}
