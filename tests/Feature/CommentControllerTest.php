<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    Spotify::shouldReceive('searchTracks->limit->get')
        ->zeroOrMoreTimes()
        ->andReturn(['tracks' => ['items' => []]]);
});

it('can get all comments', function (): void {
    $user = User::factory()->create();
    Comment::factory(5)->create();

    $response = $this->actingAs($user)->getJson('/api/comments');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data', 'links', 'meta',
        ]);
});

it('can store a comment', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/comments', [
        'post_id' => $post->id,
        'text' => 'This is a test comment.',
    ]);

    $response->assertCreated();

    assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
        'text' => 'This is a test comment.',
    ]);
});

it('can get comments for a specific post', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create();
    Comment::factory(3)->create(['post_id' => $post->id]);

    $response = $this->actingAs($user)->getJson('/api/comments/'.$post->id);

    $response->assertSuccessful()
        ->assertJsonCount(3, 'data');
});

it('returns 404 when getting comments for a non-existent post', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/comments/invalid-id');

    $response->assertNotFound();
});

it('can delete own comment', function (): void {
    $user = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson('/api/comments/'.$comment->id);

    $response->assertSuccessful();

    assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

it("cannot delete someone else's comment", function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $comment = Comment::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->deleteJson('/api/comments/'.$comment->id);

    $response->assertForbidden();

    assertDatabaseHas('comments', [
        'id' => $comment->id,
    ]);
});

it('returns 404 when deleting a non-existent comment', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->deleteJson('/api/comments/invalid-id');

    $response->assertNotFound();
});

it('creating a comment increments the post comment counter', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['count_comments' => 0]);

    $this->actingAs($user)->postJson('/api/comments', [
        'post_id' => $post->id,
        'text' => 'Counter increment test.',
    ])->assertCreated();

    expect($post->fresh()->count_comments)->toBe(1);
});

it('deleting a comment decrements the post comment counter', function (): void {
    $user = User::factory()->create();
    $post = Post::factory()->create(['count_comments' => 0]);
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
    // factory's created hook increments count_comments to 1

    $this->actingAs($user)->deleteJson('/api/comments/'.$comment->id)->assertSuccessful();

    expect($post->fresh()->count_comments)->toBe(0);
});
