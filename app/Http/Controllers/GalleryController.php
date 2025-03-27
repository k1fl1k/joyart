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

    public function search(Request $request)
    {
        $tags = Tag::all();

        $query = Artwork::query();

        // Search by tags
        if ($request->filled('search')) {
            $tag = Tag::where('name', 'like', '%' . $request->search . '%')->first();
            if ($tag) {
                return redirect()->route('gallery.byTag', $tag->slug);
            }
        }

        // Filter by file type (image or video)
        if ($request->filled('type')) {
            $query->where('file_ext', $request->type === 'video' ? 'mp4' : 'jpg');
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', '=', $request->date);
        }

        // Sort by newest or oldest
        if ($request->filled('sort')) {
            $sortOrder = $request->sort === 'oldest' ? 'asc' : 'desc';
            $query->orderBy('created_at', $sortOrder);
        }

        $images = $query->paginate(50);

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
