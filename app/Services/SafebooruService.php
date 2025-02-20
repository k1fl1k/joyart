<?php

namespace k1fl1k\joyart\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Tag;
use Illuminate\Support\Str;
use k1fl1k\joyart\Models\User;

class SafebooruService
{
    protected string $apiUrl = "https://safebooru.org/index.php?page=dapi&s=post&q=index";

    public function fetchAndStoreArtworks()
    {
        try {
            $response = Http::get($this->apiUrl);

            if ($response->failed()) {
                Log::error("API Safebooru не доступний");
                return;
            }

            $xml = simplexml_load_string($response->body());

            foreach ($xml->post as $post) {
                $this->storeArtwork($post);
            }

        } catch (\Exception $e) {
            Log::error("Помилка отримання даних з API: " . $e->getMessage());
        }
    }

    protected function storeTags($tagsString)
    {
        $tags = explode(' ', trim($tagsString));
        $tagId = null;

        foreach ($tags as $tagName) {
            if (empty($tagName)) continue;

            // Перевіряємо, чи існує тег
            $tag = Tag::where('name', $tagName)->first();

            if (!$tag) {
                $tag = new Tag([
                    'id' => (string) Str::ulid(),
                    'name' => $tagName,
                    'slug' => Str::slug($tagName),
                    'meta_title' => ucfirst($tagName),
                    'meta_description' => "Tag description for " . $tagName,
                ]);
                $tag->save();
                Log::info("✅ Створено новий тег: " . json_encode($tag->toArray()));
            } else {
                Log::info("🔹 Знайдено існуючий тег: " . json_encode($tag->toArray()));
            }

            // Зберігаємо перший знайдений тег
            if (!$tagId) {
                $tagId = $tag->id;
            }
        }

        Log::info("🟢 Повертаємо tag_id: " . $tagId);
        return $tagId;
    }

    protected function storeArtwork($post)
    {
        $md5 = (string) $post['md5'];

        if (Artwork::where('md5', $md5)->exists()) {
            return;
        }

        // Отримуємо ID першого збереженого тегу
        $tagId = $this->storeTags((string) $post['tags']);

        Log::info("🟡 tag_id для нового Artwork: " . ($tagId ?? 'NULL'));

        $adminUser = User::where('role', 'admin')->first();

        if (!$adminUser) {
            Log::error("❌ Адміністратор не знайдений. Використовується стандартний user_id.");
        } else {
            $userId = $adminUser->id;
            Log::info("👤 Знайдено адміна: " . json_encode($adminUser->toArray()));
        }
        // Створюємо новий запис про зображення
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
            'colors' => json_encode([]),
            'source' => (string) $post['source'],
            'is_published' => true,
            'slug' => Str::slug("artwork-" . $md5),
            'meta_title' => "Artwork " . $md5,
            'meta_description' => "An artwork from Safebooru",
            'image' => (string) $post['sample_url'],
            'image_alt' => "Image from Safebooru",
            'user_id' => $userId,
            'type' => 'image',
            'tag_id' => $tagId, // Додаємо перший тег до зображення
        ]);

        Log::info("📝 Artwork перед збереженням: " . json_encode($artwork->toArray()));

        $artwork->save();

        if (!$artwork->exists) {
            Log::error("❌ Не вдалося зберегти запис: " . json_encode($artwork->toArray()));
        } else {
            Log::info("✅ Запис успішно збережено: " . json_encode($artwork->toArray()));
        }
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
