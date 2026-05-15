<?php

declare(strict_types=1);

use Aerni\Spotify\Facades\Spotify;
use Aerni\Spotify\PendingRequest;
use App\Models\Music;
use App\Models\User;
use App\Services\SpotifyService;

use function Pest\Laravel\assertDatabaseHas;

it('can search and store a track from spotify', function (): void {
    $spotifyResponse = [
        'tracks' => [
            'items' => [
                [
                    'name' => 'Test Track',
                    'artists' => [
                        ['id' => 'artist1', 'name' => 'Artist One'],
                    ],
                    'album' => [
                        'images' => [['url' => 'http://example.com/image.jpg']],
                        'release_date' => '2023-01-01',
                    ],
                ],
            ],
        ],
    ];

    $mock = Mockery::mock(PendingRequest::class);
    $mock->shouldReceive('limit')->andReturn($mock);
    $mock->shouldReceive('get')->andReturn($spotifyResponse);

    Spotify::shouldReceive('searchTracks')
        ->once()
        ->with('Test Track')
        ->andReturn($mock);

    $user = User::factory()->create(['spotify_id' => 'artist1', 'musics' => 0]);

    $service = new SpotifyService();
    $music = $service->searchAndStore('Test Track');

    expect($music)->toBeInstanceOf(Music::class)
        ->and($music->title)->toBe('Test Track')
        ->and($music->artist)->toBe('Artist One');

    assertDatabaseHas('musics', [
        'title' => 'Test Track',
        'artist' => 'Artist One',
    ]);

    expect($user->refresh()->musics)->toBe(1)
        ->and($user->createdMusic->pluck('id')->contains($music->id))->toBeTrue();
});

it('returns null if track not found in spotify', function (): void {
    $mock = Mockery::mock(PendingRequest::class);
    $mock->shouldReceive('limit')->andReturn($mock);
    $mock->shouldReceive('get')->andReturn(['tracks' => ['items' => []]]);

    Spotify::shouldReceive('searchTracks')
        ->once()
        ->with('Unknown')
        ->andReturn($mock);

    $service = new SpotifyService();
    $music = $service->searchAndStore('Unknown');

    expect($music)->toBeNull();
});

it('can search in spotify without storing', function (): void {
    $spotifyResponse = [
        'tracks' => [
            'items' => [
                [
                    'id' => 'track1',
                    'name' => 'Track One',
                    'artists' => [['id' => 'artist1', 'name' => 'Artist One']],
                    'album' => [
                        'images' => [['url' => 'http://example.com/image.jpg']],
                        'release_date' => '2023-01-01',
                    ],
                ],
            ],
        ],
    ];

    $mock = Mockery::mock(PendingRequest::class);
    $mock->shouldReceive('limit')->with(5)->andReturn($mock);
    $mock->shouldReceive('get')->andReturn($spotifyResponse);

    Spotify::shouldReceive('searchTracks')
        ->once()
        ->with('Track')
        ->andReturn($mock);

    $service = new SpotifyService();
    $results = $service->searchInSpotify('Track', 5);

    expect($results)->toHaveCount(1)
        ->and($results->first())->toBeInstanceOf(Music::class)
        ->and($results->first()->id)->toBe('track1');
});

it('can get artist name', function (): void {
    Spotify::shouldReceive('artist')
        ->once()
        ->with('artist1')
        ->andReturn(Mockery::mock(PendingRequest::class, ['get' => ['name' => 'Artist One']]));

    $service = new SpotifyService();
    $name = $service->getArtistName('artist1');

    expect($name)->toBe('Artist One');
});

it('returns null if artist not found', function (): void {
    Spotify::shouldReceive('artist')
        ->once()
        ->with('unknown')
        ->andThrow(new Exception('Not found'));

    $service = new SpotifyService();
    $name = $service->getArtistName('unknown');

    expect($name)->toBeNull();
});
