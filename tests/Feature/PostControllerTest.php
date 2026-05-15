<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Models\Follow;
use App\Models\Music;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    Spotify::shouldReceive('searchTracks->limit->get')
        ->zeroOrMoreTimes()
        ->andReturn(['tracks' => ['items' => []]]);
});

it('can get all posts', function (): void {
    $user = User::factory()->create();
    Post::factory(5)->create();

    $response = $this->actingAs($user)->getJson('/api/posts');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data', 'links', 'meta',
        ]);
});

it('can store a post', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/posts', [
        'music_id' => $music->id,
        'text' => 'This is a review.',
        'rating' => 4.5,
    ]);

    $response->assertCreated();

    assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'music_id' => $music->id,
        'text' => 'This is a review.',
        'rating' => 4.5,
    ]);
});

it('cannot store duplicate post for same music', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create();

    Post::factory()->create([
        'user_id' => $user->id,
        'music_id' => $music->id,
    ]);

    $response = $this->actingAs($user)->postJson('/api/posts', [
        'music_id' => $music->id,
        'text' => 'Another review.',
        'rating' => 3,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['music_id']);
});

it('can get a single post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/posts/'.$post->id);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => ['id', 'title', 'rating', 'user_id', 'music_id'],
        ]);
});

it('can delete a post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson('/api/posts/'.$post->id);

    $response->assertSuccessful();

    assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});

it('can check if post exists for a music', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create();

    Post::factory()->create([
        'user_id' => $user->id,
        'music_id' => $music->id,
    ]);

    $response = $this->actingAs($user)->getJson('/api/posts/check/'.$music->id);

    $response->assertSuccessful()
        ->assertJson(['exists' => true]);
});

it("cannot delete another user's post", function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->deleteJson('/api/posts/'.$post->id);

    $response->assertForbidden();
});

it('returns 404 when showing non-existent post', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/posts/non-existent-uuid');

    $response->assertNotFound();
});

it('returns 404 when deleting non-existent post', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->deleteJson('/api/posts/non-existent-uuid');

    $response->assertNotFound();
});

it('can search posts by text', function (): void {
    $user = User::factory()->create();
    Post::factory()->create(['text' => 'This is a unique review text']);
    Post::factory()->create(['text' => 'Another unrelated post']);

    $response = $this->actingAs($user)->postJson('/api/posts/search', [
        'text' => 'unique review',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['data', 'links', 'meta'])
        ->assertJsonCount(1, 'data');
});

it('can search posts without filter and returns all', function (): void {
    $user = User::factory()->create();
    Post::factory(3)->create();

    $response = $this->actingAs($user)->postJson('/api/posts/search', []);

    $response->assertSuccessful()
        ->assertJsonStructure(['data', 'links', 'meta']);
});

it('check post returns false when user has no review for music', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/posts/check/'.$music->id);

    $response->assertSuccessful()
        ->assertJson(['exists' => false]);
});

it('can get followed posts', function (): void {
    $user = User::factory()->create();
    $followedUser = User::factory()->create();

    Follow::factory()->create([
        'follower_id' => $user->id,
        'followed_id' => $followedUser->id,
    ]);

    Post::factory(3)->create(['user_id' => $followedUser->id]);

    $response = $this->actingAs($user)->getJson('/api/posts/followed');

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data');
});

it('creating a post increments the user posts counter', function (): void {
    $user = User::factory()->create(['posts' => 0]);
    $music = Music::factory()->create();

    $this->actingAs($user)->postJson('/api/posts', [
        'music_id' => $music->id,
        'text' => 'Incrementing counter test.',
        'rating' => 4,
    ])->assertCreated();

    expect($user->fresh()->posts)->toBe(1);
});

it('deleting a post decrements the user posts counter', function (): void {
    $user = User::factory()->create(['posts' => 0]);
    $post = Post::factory()->create(['user_id' => $user->id]);
    // factory's created hook increments posts to 1

    $this->actingAs($user)->deleteJson('/api/posts/'.$post->id)->assertSuccessful();

    expect($user->fresh()->posts)->toBe(0);
});

it('creating a post creates a music rating record', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create();

    $this->actingAs($user)->postJson('/api/posts', [
        'music_id' => $music->id,
        'text' => 'Rating update test.',
        'rating' => 5,
    ])->assertCreated();

    $this->assertDatabaseHas('music_ratings', [
        'music_id' => $music->id,
    ]);
});
