<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;

beforeEach(function (): void {
    Spotify::shouldReceive('searchTracks->limit->get')
        ->zeroOrMoreTimes()
        ->andReturn(['tracks' => ['items' => []]]);
});

it('can get users list', function (): void {
    $user = User::factory()->create();
    User::factory(5)->create();

    $response = $this->actingAs($user)->getJson('/api/users');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data', 'links', 'meta',
        ]);
});

it('can get posts for a user', function (): void {
    $user = User::factory()->create();
    Post::factory(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson(sprintf('/api/%s/posts', $user->id));

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data', 'links', 'meta',
        ])
        ->assertJsonCount(3, 'data');
});

it('can get comments for a user', function (): void {
    $user = User::factory()->create();
    Comment::factory(4)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson(sprintf('/api/%s/comments', $user->id));

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data', 'links', 'meta',
        ])
        ->assertJsonCount(4, 'data');
});

it('can get liked items for a user', function (): void {
    $user = User::factory()->create();

    $post = Post::factory()->create();
    $comment = Comment::factory()->create();

    Like::factory()->create([
        'user_id' => $user->id,
        'likeable_id' => $post->id,
        'likeable_type' => Post::class,
    ]);

    Like::factory()->create([
        'user_id' => $user->id,
        'likeable_id' => $comment->id,
        'likeable_type' => Comment::class,
    ]);

    $response = $this->actingAs($user)->getJson(sprintf('/api/%s/likes', $user->id));

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data', 'links', 'meta',
        ])
        ->assertJsonCount(2, 'data');
});

it('can get current authenticated user', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/users/me');

    $response->assertSuccessful()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.username', $user->username);
});

it('can show a user by username', function (): void {
    $user = User::factory()->create();
    $target = User::factory()->create(['username' => 'myusername']);

    $response = $this->actingAs($user)->getJson('/api/users/myusername');

    $response->assertSuccessful()
        ->assertJsonPath('data.username', 'myusername');
});

it('returns 404 when showing non-existent username', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/users/doesnotexist');

    $response->assertNotFound();
});

it('can search users by username', function (): void {
    $user = User::factory()->create(['username' => 'searchme']);
    User::factory()->create(['username' => 'other']);

    $response = $this->actingAs($user)->getJson('/api/users/search?text=searchme');

    $response->assertSuccessful()
        ->assertJsonStructure(['data', 'links', 'meta']);
});

it('returns 404 when getting posts for non-existent user', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/non-existent-uuid/posts');

    $response->assertNotFound();
});

it('returns 404 when getting comments for non-existent user', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/non-existent-uuid/comments');

    $response->assertNotFound();
});

it('returns 401 when accessing users without authentication', function (): void {
    $response = $this->getJson('/api/users');

    $response->assertUnauthorized();
});
