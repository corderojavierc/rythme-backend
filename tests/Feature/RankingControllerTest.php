<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use App\Models\MostValoratedMusic;
use App\Models\Music;
use App\Models\MusicRating;
use App\Models\Post;
use App\Models\TopRatedMusic;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Spotify::shouldReceive('searchTracks->limit->get')
        ->zeroOrMoreTimes()
        ->andReturn(['tracks' => ['items' => []]]);
});

it('can get general top rated musics', function (): void {
    $user = User::factory()->create();

    MusicRating::factory(3)->create([
        'rating' => 4.5,
    ]);

    $response = $this->actingAs($user)->getJson('/api/musics/top-rated');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'position',
                    'rating',
                    'count_ratings',
                    'music' => [
                        'id', 'title', 'artist', 'spotify_artist_ids', 'cover_url', 'rating', 'count_ratings', 'is_valorated',
                    ],
                ],
            ],
        ]);
});

it('can get general most rated musics', function (): void {
    $user = User::factory()->create();

    MusicRating::factory(3)->create([
        'count_ratings' => 10,
    ]);

    $response = $this->actingAs($user)->getJson('/api/musics/most-rated');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'position',
                    'rating',
                    'count_ratings',
                    'music' => [
                        'id', 'title', 'artist', 'spotify_artist_ids', 'cover_url', 'rating', 'count_ratings', 'is_valorated',
                    ],
                ],
            ],
        ]);
});

it('can get actual top rated musics', function (): void {
    Cache::flush();
    $user = User::factory()->create();
    $music = Music::factory()->create();

    Post::factory()->create([
        'music_id' => $music->id,
        'rating' => 5,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->getJson('/api/musics/top-rated/actual');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'period',
            'data' => [
                '*' => [
                    'position',
                    'rating',
                    'count_ratings',
                    'music' => [
                        'id', 'title', 'artist', 'spotify_artist_ids', 'cover_url', 'rating', 'count_ratings', 'is_valorated',
                    ],
                ],
            ],
        ]);
});

it('can get actual most rated musics', function (): void {
    Cache::flush();
    $user = User::factory()->create();
    $music = Music::factory()->create();

    Post::factory(2)->create([
        'music_id' => $music->id,
        'created_at' => now(),
    ]);

    $response = $this->actingAs($user)->getJson('/api/musics/most-rated/actual');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'period',
            'data' => [
                '*' => [
                    'position',
                    'rating',
                    'count_ratings',
                    'music' => [
                        'id', 'title', 'artist', 'spotify_artist_ids', 'cover_url', 'rating', 'count_ratings', 'is_valorated',
                    ],
                ],
            ],
        ]);
});

it('can get top rated history for past period', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create();
    $period = now()->subMonth()->format('Y-m');
    $date = now()->subMonth()->startOfMonth()->toDateString();

    TopRatedMusic::factory()->create([
        'music_id' => $music->id,
        'period' => $date,
        'rank_position' => 1,
    ]);

    $response = $this->actingAs($user)->getJson('/api/musics/top-rated-history/'.$period);

    $response->assertSuccessful()
        ->assertJson([
            'period' => $period,
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'position',
                    'rating',
                    'count_ratings',
                    'music' => [
                        'id', 'title', 'artist', 'spotify_artist_ids', 'cover_url', 'rating', 'count_ratings', 'is_valorated',
                    ],
                ],
            ],
        ]);
});

it('can get most rated history for past period', function (): void {
    $user = User::factory()->create();
    $music = Music::factory()->create();
    $period = now()->subMonth()->format('Y-m');
    $date = now()->subMonth()->startOfMonth()->toDateString();

    MostValoratedMusic::factory()->create([
        'music_id' => $music->id,
        'period' => $date,
        'rank_position' => 1,
    ]);

    $response = $this->actingAs($user)->getJson('/api/musics/most-rated-history/'.$period);

    $response->assertSuccessful()
        ->assertJson([
            'period' => $period,
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'position',
                    'rating',
                    'count_ratings',
                    'music' => [
                        'id', 'title', 'artist', 'spotify_artist_ids', 'cover_url', 'rating', 'count_ratings', 'is_valorated',
                    ],
                ],
            ],
        ]);
});

it('returns validation error for invalid history period format', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/musics/top-rated-history/invalid-format');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['period']);
});
