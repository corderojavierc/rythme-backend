<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Enums\ArtistApplicationStatusEnum;
use App\Models\ArtistApplication;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    Spotify::shouldReceive('searchTracks->limit->get')
        ->zeroOrMoreTimes()
        ->andReturn(['tracks' => ['items' => []]]);
});

it('can get all artist applications', function (): void {
    $user = User::factory()->create();
    ArtistApplication::factory(3)->create();

    $response = $this->actingAs($user)->getJson('/api/artist-applications');

    $response->assertSuccessful()
        ->assertJsonCount(3);
});

it('can store an artist application', function (): void {
    $user = User::factory()->create();

    $data = [
        'type' => 'artist',
        'followers' => 1000,
        'description' => 'Test application description',
    ];

    $response = $this->actingAs($user)->postJson('/api/artist-applications', $data);

    $response->assertCreated()
        ->assertJsonFragment([
            'user_id' => $user->id,
            'type' => 'artist',
        ]);

    assertDatabaseHas('artist_applications', [
        'user_id' => $user->id,
        'type' => 'artist',
        'status' => ArtistApplicationStatusEnum::SENT->value,
    ]);
});

it('cannot store an artist application if one is already pending', function (): void {
    $user = User::factory()->create();

    ArtistApplication::factory()->create([
        'user_id' => $user->id,
        'status' => ArtistApplicationStatusEnum::SENT,
    ]);

    $data = [
        'type' => 'creator',
        'description' => 'Another application description',
    ];

    $response = $this->actingAs($user)->postJson('/api/artist-applications', $data);

    $response->assertStatus(409)
        ->assertJson(['message' => 'Error: ya tienes una solicitud de artista pendiente o aceptada.']);
});

it('can check if user has pending application', function (): void {
    $user = User::factory()->create();

    ArtistApplication::factory()->create([
        'user_id' => $user->id,
        'status' => ArtistApplicationStatusEnum::SENT,
    ]);

    $response = $this->actingAs($user)->getJson('/api/artist-applications/has');

    $response->assertSuccessful()
        ->assertJson(['has_application' => true]);
});

it('can check if user does not have pending application', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/artist-applications/has');

    $response->assertSuccessful()
        ->assertJson(['has_application' => false]);
});
