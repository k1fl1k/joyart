<?php

namespace k1fl1k\joyart\Http\Controllers;

use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Http\Requests\StoreArtworkRequest;
use k1fl1k\joyart\Http\Requests\UpdateArtworkRequest;
use k1fl1k\joyart\Models\Tag;

class ArtworkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArtworkRequest $request)
    {
        //
    }

    public function show(Artwork $artwork)
    {
        $tags = Tag::with('subtags')->get();
        $user = $artwork->user; // Автор
        $comments = $artwork->comments()->paginate(5);

        return view('artwork', compact('artwork', 'tags', 'user', 'comments'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Artwork $artwork)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArtworkRequest $request, Artwork $artwork)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artwork $artwork)
    {
        //
    }
}
