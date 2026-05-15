<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    Spotify::shouldReceive('searchTracks->limit->get')
        ->zeroOrMoreTimes()
        ->andReturn(['tracks' => ['items' => []]]);
});

it('can get likes for a user', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    Like::factory()->create([
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);

    $response = $this->actingAs($user)->getJson('/api/likes/'.$user->id);

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('can create a like', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/likes', [
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);

    $response->assertCreated();

    assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);
});

it('cannot create a duplicate like', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    Like::factory()->create([
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);

    $response = $this->actingAs($user)->postJson('/api/likes', [
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);

    $response->assertStatus(409)
        ->assertJson(['message' => 'Error: ya has dado like a este elemento.']);
});

it('can like a comment', function (): void {
    $user = User::factory()->create();
    $comment = Comment::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/likes', [
        'user_id' => $user->id,
        'likeable_id' => $comment->id,
        'likeable_type' => Comment::class,
    ]);

    $response->assertCreated()
        ->assertJson(['message' => 'Like añadido correctamente.']);
});

it('returns 404 when destroying a non-existent like', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->deleteJson('/api/likes', [
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);

    $response->assertNotFound();
});

it('can destroy a like', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    Like::factory()->create([
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);

    $response = $this->actingAs($user)->deleteJson('/api/likes', [
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);

    $response->assertSuccessful()
        ->assertJson(['message' => 'Like eliminado correctamente.']);

    assertDatabaseMissing('likes', [
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);
});
