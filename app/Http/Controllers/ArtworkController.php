<?php

namespace k1fl1k\joyart\Http\Controllers;

use FFMpeg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Tag;
use k1fl1k\joyart\Models\User;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

class ArtworkController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Processing artwork upload request.', ['user_id' => auth()->id()]);

        try {
            $validated = $request->validate([
                'type' => 'required|in:image,animation,video',
                'rating' => 'required|in:general,sensitive,questionable',
                'is_vip' => 'required|boolean',
                'meta_title' => 'required|string|max:128|unique:artworks,meta_title',
                'meta_description' => 'required|string|max:278',
                'image_alt' => 'nullable|string|max:256',
                'tags' => 'nullable|string',
                'original' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:51200',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);

            return back()->withErrors($e->errors());
        }

        try {
            Log::info('File validation successful.', ['file' => $request->file('original')->getClientOriginalName()]);

            $file = $request->file('original');
            $md5 = md5_file($file->getRealPath());

            if (Artwork::where('md5', $md5)->exists()) {
                Log::warning('Duplicate file detected.', ['md5' => $md5]);

                return back()->withErrors(['original' => 'This file already exists.']);
            }

            Log::info('MD5 checksum calculated.', ['md5' => $md5]);

            $extension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize();
            $filePath = $file->store('artworks/originals', 'public');

            // Отримуємо публічний URL для файлу
            $fileUrl = Storage::url($filePath);

            Log::info('File stored successfully.', ['path' => $filePath, 'url' => $fileUrl]);

            $slug = $this->generateUniqueSlug($validated['meta_title']);
            Log::info('Generated unique slug.', ['slug' => $slug]);

            list($thumbnailPath, $width, $height) = $this->generateThumbnail($file, $extension);
            Log::info('Thumbnail generated.', ['thumbnail_path' => $thumbnailPath, 'width' => $width, 'height' => $height]);

            $colors = $this->extractColors($filePath);
            Log::info('Extracted colors from image.', ['colors' => $colors]);

            $userId = auth()->id() ?? User::where('role', 'admin')->value('id');
            Log::info('Determined user ID.', ['user_id' => $userId]);

            $artwork = Artwork::create([
                'id' => (string) Str::ulid(),
                'user_id' => $userId,
                'md5' => $md5,
                'type' => $validated['type'],
                'rating' => $validated['rating'],
                'width' => $width, // Заглушка, потрібно визначати
                'height' => $height, // Заглушка, потрібно визначати
                'file_ext' => $extension,
                'file_size' => $fileSize,
                'thumbnail' => $thumbnailPath,
                'original' => $fileUrl,  // зберігаємо URL, а не локальний шлях
                'is_vip' => $validated['is_vip'],
                'colors' => json_encode($colors),
                'source' => $request->input('source', null),
                'is_published' => true,
                'slug' => $slug,
                'meta_title' => $validated['meta_title'],
                'meta_description' => $validated['meta_description'],
                'image' => $fileUrl,
                'image_alt' => $validated['image_alt'] ?? null,
            ]);

            Log::info('Artwork saved to database.', ['artwork_id' => $artwork->id]);

            if (! empty($validated['tags'])) {
                $tagIds = $this->storeTags($validated['tags']);
                $artwork->tags()->attach($tagIds);
                Log::info('Tags attached to artwork.', ['tags' => $tagIds]);
            }

            return redirect()->route('welcome')->with('success', 'Artwork created successfully.');
        } catch (\Exception $e) {
            Log::error('Error saving artwork: '.$e->getMessage());

            return back()->withErrors(['error' => 'An error occurred while saving the artwork.']);
        }
    }

    protected function generateUniqueSlug($baseSlug)
    {
        $slug = Str::slug($baseSlug);
        $count = 1;
        while (Artwork::where('slug', $slug)->exists()) {
            $slug = Str::slug($baseSlug).'-'.$count;
            $count++;
        }

        Log::info('Final unique slug generated.', ['slug' => $slug]);

        return $slug;
    }

    protected function generateThumbnail($file, $extension)
    {
        Log::info('Generating thumbnail for file.', [
            'extension' => $extension,
            'filename' => $file->getClientOriginalName()
        ]);

        try {
            // Define thumbnail storage path and filename
            $thumbnailDir = 'thumbnails';
            $thumbnailFilename = 'thumb_' . time() . '_' . Str::random(8) . '.jpg';
            $thumbnailPath = "$thumbnailDir/$thumbnailFilename";

            // Initialize Intervention Image manager
            $manager = new ImageManager(new Driver());
            $width = 0;
            $height = 0;

            if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
                // Handle static images
                $image = $manager->read($file->getRealPath());
                $width = $image->width();
                $height = $image->height();
                $image->resize(150, 150, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                Storage::disk('public')->put($thumbnailPath, $image->toJpeg(75));

            }  elseif (in_array($extension, ['gif', 'mp4', 'mov', 'avi'])) {
                $ffmpeg = FFMpeg\FFMpeg::create();
                $video = $ffmpeg->open($file->getRealPath());
                $frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10));
                $framePath = storage_path('app/public/thumbnails/' . $thumbnailFilename);
                $frame->save($framePath);

                $image = $manager->read($framePath);
                $width = $image->width();
                $height = $image->height();
            } else {
                throw new \Exception("Unsupported file extension: $extension");
            }

            $thumbnailUrl = Storage::disk('public')->url($thumbnailPath);

            Log::info('Thumbnail generated successfully.', [
                'path' => $thumbnailPath,
                'url' => $thumbnailUrl,
                'width' => $width,
                'height' => $height
            ]);

            return [$thumbnailUrl, $width, $height];

        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to generate thumbnail: ' . $e->getMessage());
        }
    }

    protected function extractColors($filePath)
    {
        try {
            $palette = Palette::fromFilename(storage_path('app/public/'.$filePath));
            $extractor = new ColorExtractor($palette);
            $colors = array_map(fn ($color) => sprintf('#%06X', $color), $extractor->extract(4));

            Log::info('Extracted colors from image.', ['colors' => $colors]);

            return $colors;
        } catch (\Exception $e) {
            Log::error('Error extracting colors: '.$e->getMessage());

            return [];
        }
    }

    protected function storeTags($tagsString)
    {
        $tags = explode(',', trim($tagsString));
        $tagIds = [];

        foreach ($tags as $tagName) {
            $tagName = trim($tagName);
            if (empty($tagName)) {
                continue;
            }

            $tag = Tag::firstOrCreate(
                ['name' => $tagName],
                [
                    'id' => (string) Str::ulid(),
                    'slug' => Str::slug($tagName),
                    'meta_title' => ucfirst($tagName),
                    'meta_description' => 'Tag description for '.$tagName,
                ]
            );

            Log::info('Tag processed.', ['tag_name' => $tagName, 'tag_id' => $tag->id]);

            $tagIds[] = $tag->id;
        }

        Log::info('Tags stored successfully.', ['tag_ids' => $tagIds]);

        return $tagIds;
    }

    public function show(Artwork $artwork)
    {
        Log::info('Displaying artwork details.', ['artwork_id' => $artwork->id]);

        $tags = Tag::with('subtags')->get();
        $user = $artwork->user;
        $comments = $artwork->comments()->paginate(5);

        return view('artwork', compact('artwork', 'tags', 'user', 'comments'));
    }

    public function edit(Artwork $artwork)
    {
        if (auth()->id() !== $artwork->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('artwork.edit', compact('artwork'));
    }

    public function update(Request $request, Artwork $artwork)
    {
        $request->validate([
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'image_alt' => 'nullable|string|max:255',
            'tags' => 'nullable|string', // Приймаємо теги у вигляді рядка
        ]);

        // Оновлення даних
        $artwork->update([
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'image_alt' => $request->image_alt,
            'is_published' => $request->has('is_published'),
        ]);

        // Оновлення тегів
        if (! empty($request->tags)) {
            $tagNames = explode(',', $request->tags);

            $tags = collect($tagNames)->map(function ($tag) {
                return Tag::firstOrCreate(
                    ['name' => trim($tag)],
                    [
                        'id' => (string) Str::ulid(), // Генеруємо ULID для нових тегів
                        'slug' => Str::slug($tag),
                        'meta_title' => ucfirst($tag),
                        'meta_description' => 'Tag description for '.$tag,
                    ]
                );
            });

            $artwork->tags()->sync($tags->pluck('id'));
        } else {
            $artwork->tags()->detach();
        }

        return redirect()->route('artwork.show', $artwork->slug)->with('success', 'Artwork updated successfully!');
    }

    public function destroy(Artwork $artwork)
    {
        $artwork->delete();

        return redirect()->route('welcome')->with('success', 'Artwork deleted successfully.');
    }
}
