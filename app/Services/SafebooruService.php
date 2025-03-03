<?php

namespace k1fl1k\joyart\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Tag;
use k1fl1k\joyart\Models\User;
use League\ColorExtractor\Palette;
use League\ColorExtractor\ColorExtractor;

class SafebooruService
{
    protected string $apiUrl = 'https://safebooru.org/index.php?page=dapi&s=post&q=index&limit=100'; // Наприклад, обмежимо до 100 записів

    public function fetchAndStoreArtworks(int $maxImages = 10) // Додаємо параметр для обмеження
    {
        try {
            $response = Http::get($this->apiUrl);

            if ($response->failed()) {
                Log::error('API Safebooru не доступний');
                return;
            }

            $xml = simplexml_load_string($response->body());
            $count = 0;

            foreach ($xml->post as $post) {
                if ($count >= $maxImages) {
                    break; // Зупиняємо, якщо досягли ліміту
                }

                $this->storeArtwork($post);
                $count++;
            }
        } catch (\Exception $e) {
            Log::error('Помилка отримання даних з API: ' . $e->getMessage());
        }
    }

    protected function storeTags($tagsString)
    {
        $tags = explode(' ', trim($tagsString));
        $tagIds = [];

        foreach ($tags as $tagName) {
            if (empty($tagName)) continue;

            $existingTag = Tag::where('name', $tagName)->first();

            if ($existingTag) {
                $tagIds[] = $existingTag->id;
            } else {
                $slug = $this->generateUniqueSlug($tagName);

                $tag = Tag::create([
                    'id' => (string) Str::ulid(),
                    'name' => $tagName,
                    'slug' => $slug,
                    'meta_title' => ucfirst($tagName),
                    'meta_description' => 'Tag description for ' . $tagName,
                ]);

                $tagIds[] = $tag->id;
            }
        }

        return $tagIds;
    }


    protected function storeArtwork($post)
    {
        $md5 = (string) $post['md5'];

        if (Artwork::where('md5', $md5)->exists()) {
            return;
        }

        $tagIds = $this->storeTags((string) $post['tags']);
        $userId = User::where('role', 'admin')->value('id') ?? null;

        $artwork = new Artwork([
            'id' => (string) Str::ulid(),
            'md5' => $md5,
            'rating' => $this->mapRating((string) $post['rating']),
            'width' => (int) $post['width'],
            'height' => (int) $post['height'],
            'file_ext' => pathinfo((string) $post['file_url'], PATHINFO_EXTENSION),
            'file_size' => 0,
            'thumbnail' => (string) $post['preview_url'],
            'original' => (string) $post['file_url'],
            'is_vip' => false,
            'colors' => json_encode($this->extractColors((string) $post['file_url'])),
            'source' => (string) $post['source'],
            'is_published' => true,
            'slug' => $this->generateUniqueSlug('artwork-' . $md5),
            'meta_title' => 'Artwork ' . $md5,
            'meta_description' => 'An artwork from Safebooru',
            'image' => (string) $post['sample_url'],
            'image_alt' => 'Image from Safebooru',
            'user_id' => $userId,
            'type' => 'image',
        ]);

        $artwork->save();
        $artwork->tags()->attach($tagIds);
    }

    protected function extractColors($imageUrl)
    {
        try {
            $imagePath = tempnam(sys_get_temp_dir(), 'color');
            file_put_contents($imagePath, file_get_contents($imageUrl));

            $palette = Palette::fromFilename($imagePath);
            $extractor = new ColorExtractor($palette);
            $colors = $extractor->extract(5);

            unlink($imagePath);
            return array_map(fn($color) => sprintf("#%06X", $color), $colors);
        } catch (\Exception $e) {
            Log::error('Помилка визначення кольорів: ' . $e->getMessage());
            return [];
        }
    }

    protected function generateUniqueSlug($baseSlug)
    {
        $slug = Str::slug($baseSlug);
        $count = 1;

        while (Artwork::where('slug', $slug)->exists()) {
            $slug = Str::slug($baseSlug) . '-' . $count;
            $count++;
        }
        while (Tag::where('slug', $slug)->exists()) { // Перевіряємо в `tags`
            $slug = Str::slug($baseSlug) . '-' . $count;
            $count++;
        }

        return $slug;
    }

    protected function mapRating(string $rating)
    {
        return match ($rating) {
            'g' => 'general',
            's' => 'sensitive',
            'q' => 'questionable',
            default => 'general',
        };
    }
}
