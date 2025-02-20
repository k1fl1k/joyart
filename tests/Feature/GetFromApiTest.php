<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;

class GetFromApiTest extends TestCase
{
    /**
     * Test fetching data from Safebooru API.
     */
    public function test_fetch_safebooru_data(): void
    {
        // Виконуємо GET-запит до API
        $response = Http::get('https://safebooru.org/index.php?page=dapi&s=post&q=index&limit=1');

        // Перевіряємо, що статус відповіді 200 (успішний запит)
        $this->assertTrue($response->successful(), 'API request failed');

        // Отримуємо тіло відповіді (XML)
        $xml = simplexml_load_string($response->body());

        // Перевіряємо, що отримані дані не порожні
        $this->assertNotEmpty($xml, 'Received empty XML response');

        // Виводимо інформацію про перший пост у консоль
        foreach ($xml->post as $post) {
            dump([
                'id' => (string) $post['id'],
                'md5' => (string) $post['md5'],
                'rating' => (string) $post['rating'],
                'width' => (int) $post['width'],
                'height' => (int) $post['height'],
                'file_url' => (string) $post['file_url'],
                'source' => (string) $post['source'],
                'tags' => (string) $post['tags'],
            ]);
        }
    }
}
