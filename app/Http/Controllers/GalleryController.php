<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Tag;

class GalleryController extends Controller
{
    public function index()
    {
        $tags = Tag::with('subtags')->get();
        $images = Artwork::paginate(50);

        return view('welcome', compact('tags', 'images'));
    }

    public function filterByTag(Tag $tag)
    {
        $tags = Tag::with('subtags')->get();
        $images = Artwork::whereHas('tags', function ($query) use ($tag) {
            $query->where('tags.id', $tag->id);
        })->paginate(50);

        return view('welcome', compact('tags', 'images', 'tag'));
    }
}
