<?php

declare(strict_types=1);

namespace App\Services;

use Aerni\Spotify\Facades\Spotify;
use App\Models\Music;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;

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
        } catch (Exception) {
            return null;
        }
    }

    public static function searchInSpotify(string $query, int $limit = 5): Collection
    {
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
    }

    public static function getArtistName(string $artistId): ?string
    {
        return cache()->remember('spotify_artist_name_'.$artistId, now()->addMonth(), function () use ($artistId) {
            try {
                $artist = Spotify::artist($artistId)->get();

                return $artist['name'] ?? null;
            } catch (Exception) {
                return null;
            }
        });
    }
}
