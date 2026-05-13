<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Models\Follow;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function (): void {
    Spotify::shouldReceive('searchTracks->limit->get')
        ->zeroOrMoreTimes()
        ->andReturn(['tracks' => ['items' => []]]);
});

it('can get follows for a user', function (): void {
    $follower = User::factory()->create();
    $followed = User::factory()->create();

    Follow::factory()->create([
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ]);

    $response = $this->actingAs($follower)->getJson('/api/follows/'.$follower->id);

    $response->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('can create a follow', function (): void {
    $follower = User::factory()->create();
    $followed = User::factory()->create();

    $response = $this->actingAs($follower)->postJson('/api/follows', [
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ]);

    $response->assertCreated();

    assertDatabaseHas('follows', [
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ]);
});

it('cannot follow oneself', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/follows', [
        'follower_id' => $user->id,
        'followed_id' => $user->id,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['follower_id']);
});

it('cannot follow twice', function (): void {
    $follower = User::factory()->create();
    $followed = User::factory()->create();

    Follow::factory()->create([
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ]);

    $response = $this->actingAs($follower)->postJson('/api/follows', [
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ]);

    $response->assertStatus(409)
        ->assertJson(['message' => 'Error: ya sigues a este usuario.']);
});

it('can destroy a follow', function (): void {
    $follower = User::factory()->create();
    $followed = User::factory()->create();

    Follow::factory()->create([
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ]);

    $response = $this->actingAs($follower)->deleteJson('/api/follows', [
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ]);

    $response->assertSuccessful()
        ->assertJson(['message' => 'Has dejado de seguir al usuario correctamente.']);

    assertDatabaseMissing('follows', [
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ]);
});

it('creating a follow updates follower and following counters', function (): void {
    $follower = User::factory()->create(['following' => 0]);
    $followed = User::factory()->create(['followers' => 0]);

    $this->actingAs($follower)->postJson('/api/follows', [
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ])->assertCreated();

    expect($follower->fresh()->following)->toBe(1);
    expect($followed->fresh()->followers)->toBe(1);
});

it('destroying a follow decrements follower and following counters', function (): void {
    $follower = User::factory()->create(['following' => 0]);
    $followed = User::factory()->create(['followers' => 0]);

    Follow::factory()->create([
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ]);
    // factory's created hook increments following and followers to 1

    $this->actingAs($follower)->deleteJson('/api/follows', [
        'follower_id' => $follower->id,
        'followed_id' => $followed->id,
    ])->assertSuccessful();

    expect($follower->fresh()->following)->toBe(0);
    expect($followed->fresh()->followers)->toBe(0);
});
