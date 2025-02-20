<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Tag;

class WelcomeController extends Controller
{
    public function showWelcome()
    {
        $tags = Tag::whereNull('parent_id')->with('subtags')->get();
        $images = Artwork::all();

        // Повертаємо вигляд і передаємо дані
        return view('welcome', compact('tags', 'images'));

    }
}
