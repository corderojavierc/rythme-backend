<?php

declare(strict_types=1);

namespace App\Services;

use Aerni\Spotify\Facades\Spotify;
use App\Models\Music;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

final class SpotifyService
{
    public static function searchAndStore(string $query): ?Music
    {
        try {
            $results = Spotify::searchTracks($query)->limit(1)->get();
            $track = $results['tracks']['items'][0] ?? null;

            if (! $track) {
                return null;
            }

            $artists = collect($track['artists']);
            $artistNames = $artists->pluck('name')->implode(', ');
            $artistIds = $artists->pluck('id')->toArray();

            $music = Music::query()->firstOrCreate([
                'title' => $track['name'],
                'artist' => $artistNames,
            ], [
                'spotify_artist_ids' => $artistIds,
                'cover_url' => $track['album']['images'][0]['url'] ?? '',
                'release_date' => $track['album']['release_date'],
            ]);

            if ($music->wasRecentlyCreated) {
                $users = User::query()->whereIn('spotify_id', $artistIds)->get();

                foreach ($users as $user) {
                    $user->createdMusic()->syncWithoutDetaching([$music->id]);
                    $user->increment('musics');
                }
            }

            return $music;
        } catch (Throwable $throwable) {
            Log::warning('SpotifyService (searchAndStore) error: '.$throwable->getMessage(), [
                'query' => $query,
            ]);

            return null;
        }
    }

    public static function searchInSpotify(string $query, int $limit = 5): Collection
    {
        try {
            $results = Spotify::searchTracks($query)->limit($limit)->get();
            $items = $results['tracks']['items'] ?? [];

            return collect($items)->map(function (array $track): Music {
                $artists = collect($track['artists']);

                return new Music([
                    'id' => $track['id'],
                    'title' => $track['name'],
                    'artist' => $artists->pluck('name')->implode(', '),
                    'spotify_artist_ids' => $artists->pluck('id')->toArray(),
                    'cover_url' => $track['album']['images'][0]['url'] ?? '',
                    'release_date' => $track['album']['release_date'],
                ]);
            });
        } catch (Throwable $throwable) {
            Log::warning('SpotifyService (searchInSpotify) error: '.$throwable->getMessage(), [
                'query' => $query,
            ]);

            return collect();
        }
    }

    public static function getArtistName(string $artistId): ?string
    {
        return cache()->remember('spotify_artist_name_'.$artistId, now()->addMonth(), function () use ($artistId) {
            try {
                $artist = Spotify::artist($artistId)->get();

                return $artist['name'] ?? null;
            } catch (Throwable $throwable) {
                Log::warning('SpotifyService (getArtistName) error: '.$throwable->getMessage(), [
                    'artist_id' => $artistId,
                ]);

                return null;
            }
        });
    }
}
