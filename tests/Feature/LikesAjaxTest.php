<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\User;
use Tests\TestCase;

class LikesAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_ajax_like_toggle_requires_authentication()
    {
        $artwork = Artwork::factory()->create();

        $response = $this->postJson(route('likes.toggle', $artwork->slug));

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'You must be logged in to like artworks.',
                 ]);
    }

    public function test_ajax_like_artwork_successfully()
    {
        $user = User::factory()->create();
        $artwork = Artwork::factory()->create();

        $response = $this->actingAs($user)
                         ->postJson(route('likes.toggle', $artwork->slug));

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'isLiked' => true,
                     'likesCount' => 1,
                     'message' => 'Artwork liked!'
                 ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'artwork_id' => $artwork->id,
            'state' => 'like'
        ]);
    }

    public function test_ajax_unlike_artwork_successfully()
    {
        $user = User::factory()->create();
        $artwork = Artwork::factory()->create();

        // First like the artwork
        $this->actingAs($user)
             ->postJson(route('likes.toggle', $artwork->slug));

        // Then unlike it
        $response = $this->actingAs($user)
                         ->postJson(route('likes.toggle', $artwork->slug));

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'isLiked' => false,
                     'likesCount' => 0,
                     'message' => 'Like removed.'
                 ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'artwork_id' => $artwork->id
        ]);
    }

    public function test_ajax_like_count_updates_correctly()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $artwork = Artwork::factory()->create();

        // User 1 likes
        $response1 = $this->actingAs($user1)
                          ->postJson(route('likes.toggle', $artwork->slug));

        $response1->assertJson(['likesCount' => 1]);

        // User 2 likes
        $response2 = $this->actingAs($user2)
                          ->postJson(route('likes.toggle', $artwork->slug));

        $response2->assertJson(['likesCount' => 2]);

        // User 1 unlikes
        $response3 = $this->actingAs($user1)
                          ->postJson(route('likes.toggle', $artwork->slug));

        $response3->assertJson(['likesCount' => 1]);
    }

    public function test_non_ajax_request_redirects_back()
    {
        $user = User::factory()->create();
        $artwork = Artwork::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('likes.toggle', $artwork->slug));

        $response->assertRedirect();
    }

    public function test_ajax_request_with_invalid_artwork_returns_404()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->postJson('/artworks/non-existent-slug/likes');

        $response->assertStatus(404);
    }

    public function test_multiple_rapid_like_requests_handled_correctly()
    {
        $user = User::factory()->create();
        $artwork = Artwork::factory()->create();

        // Simulate rapid clicking
        $responses = [];
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->actingAs($user)
                                ->postJson(route('likes.toggle', $artwork->slug));
        }

        // Should end up with artwork being liked (odd number of clicks)
        $lastResponse = end($responses);
        $lastResponse->assertJson(['isLiked' => true]);

        // Should only have one like record in database
        $this->assertEquals(1, $artwork->likes()->count());
    }
}
