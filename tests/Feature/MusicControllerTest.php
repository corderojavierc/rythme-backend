<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Enums\UserTypeEnum;
use App\Models\Music;
use App\Models\Post;
use App\Models\User;

it('can get empty index', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/music');

    $response->assertSuccessful()
        ->assertJson([]);
});

it('can find and store music locally', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create([
        'title' => 'Test Song',
        'artist' => 'Test Artist',
    ]);

    $response = $this->actingAs($user)->postJson('/api/music', [
        'name' => 'Test Song',
    ]);

    $response->assertSuccessful()
        ->assertJsonFragment([
            'title' => 'Test Song',
        ]);
});

it('can store music via spotify if not found locally', function (): void {
    $user = User::factory()->create();

    $mockMusic = Music::factory()->make([
        'title' => 'Spotify Song',
    ]);

    $mockResults = [
        'tracks' => [
            'items' => [
                [
                    'name' => 'Spotify Song',
                    'artists' => [['name' => 'Spotify Artist', 'id' => '123']],
                    'album' => [
                        'images' => [['url' => 'http://example.com/image.jpg']],
                        'release_date' => '2023-01-01',
                    ],
                ],
            ],
        ],
    ];

    Spotify::shouldReceive('searchTracks->limit->get')
        ->once()
        ->andReturn($mockResults);

    $response = $this->actingAs($user)->postJson('/api/music', [
        'name' => 'Spotify Song',
    ]);

    $response->assertSuccessful()
        ->assertJsonFragment([
            'title' => 'Spotify Song',
        ]);
});

it('returns 404 if music not found anywhere', function (): void {
    $user = User::factory()->create();

    Spotify::shouldReceive('searchTracks->limit->get')
        ->once()
        ->andReturn(['tracks' => ['items' => []]]);

    $response = $this->actingAs($user)->postJson('/api/music', [
        'name' => 'Unknown Song',
    ]);

    $response->assertNotFound()
        ->assertJson(['message' => 'Error: la canción no ha sido encontrada en ninguna plataforma.']);
});

it('can search for music locally', function (): void {
    $user = User::factory()->create();
    $musics = Music::factory(5)->make([
        'title' => 'Searchable Song',
    ]);

    foreach ($musics as $music) {
        $music->save();
    }

    Spotify::shouldReceive('searchTracks->limit->get')
        ->once()
        ->andReturn(['tracks' => ['items' => []]]);

    $response = $this->actingAs($user)->postJson('/api/music/search', [
        'name' => 'Searchable',
    ]);

    $response->assertSuccessful()
        ->assertJsonCount(5, 'data');
});

it('can show music by id', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/music/'.$music->id);

    $response->assertSuccessful()
        ->assertJsonFragment([
            'id' => $music->id,
        ]);
});

it('can get posts for music', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create();
    Post::factory(3)->create(['music_id' => $music->id]);

    $response = $this->actingAs($user)->getJson(sprintf('/api/music/%s/posts', $music->id));

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data', 'links', 'meta',
        ])
        ->assertJsonCount(3, 'data');
});

it('returns 404 when showing non-existent music', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/music/non-existent-uuid');

    $response->assertNotFound();
});

it('returns 404 when getting posts for non-existent music', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/music/non-existent-uuid/posts');

    $response->assertNotFound();
});

it('can get musics for an artist user', function (): void {
    $artist = User::factory()->create(['type' => UserTypeEnum::ARTIST]);
    $music = Music::factory()->create();
    $artist->createdMusic()->attach($music->id);

    $response = $this->actingAs($artist)->getJson(sprintf('/api/music/%s/musics', $artist->id));

    $response->assertSuccessful()
        ->assertJsonStructure(['data', 'links', 'meta'])
        ->assertJsonCount(1, 'data');
});

it('returns 403 when getting musics for a non-artist user', function (): void {
    $user = User::factory()->create(['type' => UserTypeEnum::USER]);
    $auth = User::factory()->create();

    $response = $this->actingAs($auth)->getJson(sprintf('/api/music/%s/musics', $user->id));

    $response->assertForbidden();
});

it('returns 404 when getting musics for non-existent user', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/music/non-existent-uuid/musics');

    $response->assertNotFound();
});

it('returns validation error when searching music with empty name', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/music/search', [
        'name' => '',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});
