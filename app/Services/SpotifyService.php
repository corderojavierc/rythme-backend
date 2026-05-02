<?php

declare(strict_types=1);

namespace App\Services;

use Aerni\Spotify\Facades\Spotify;
use App\Models\Music;
use Exception;
use Illuminate\Support\Collection;

final class SpotifyService
{
    public static function searchAndStore(string $query): ?Music
    {
        try {
            $results = Spotify::searchTracks($query)->limit(1)->get();
            /** @var array<string, mixed>|null $track */
            $track = $results['tracks']['items'][0] ?? null;

            if (! $track) {
                return null;
            }

            /** @var Music $music */
            $music = Music::query()->firstOrCreate([
                'title' => $track['name'],
                'artist' => $track['artists'][0]['name'],
            ], [
                'cover_url' => $track['album']['images'][0]['url'] ?? '',
                'release_date' => $track['album']['release_date'],
            ]);

            return $music;
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @return Collection<int, Music>
     */
    public static function searchInSpotify(string $query, int $limit = 5): Collection
    {
        $results = Spotify::searchTracks($query)->limit($limit)->get();
        /** @var array<int, array<string, mixed>> $items */
        $items = $results['tracks']['items'] ?? [];

        return collect($items)->map(fn (array $track): Music => new Music([
            'id' => $track['id'],
            'title' => $track['name'],
            'artist' => $track['artists'][0]['name'],
            'cover_url' => $track['album']['images'][0]['url'] ?? '',
            'release_date' => $track['album']['release_date'],
        ]));
    }

    public static function getArtistName(string $artistId): ?string
    {
        try {
            /** @var array<string, mixed> $artist */
            $artist = Spotify::artist($artistId)->get();

            return $artist['name'] ?? null;
        } catch (Exception) {
            return null;
        }
    }
}
