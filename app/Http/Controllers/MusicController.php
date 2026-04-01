<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Aerni\Spotify\Facades\Spotify;
use App\Models\Music;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

final class MusicController extends Controller
{
    public function index(): Factory|View
    {
        return view('music.search');
    }

    public function searchSpotify(Request $request)
    {
        $query = $request->input('query');
        if (! $query) {
            return response()->json(['error' => 'Escribe algo'], 400);
        }

        try {
            Http::withoutVerifying();

            $results = Spotify::searchTracks($query)->limit(5)->get();
            $items = $results['tracks']['items'] ?? [];

            $tracks = collect($items)->map(fn ($track): array => [
                'spotify_id' => $track['id'],
                'title' => $track['name'],
                'artist' => $track['artists'][0]['name'],
                'cover_url' => $track['album']['images'][0]['url'] ?? '',
                'release_date' => $track['album']['release_date'],
            ]);

            return response()->json($tracks);
        } catch (Exception $exception) {
            return response()->json(['error' => 'Error buscando: '.$exception->getMessage()], 500);
        }
    }

    public function saveTrack(Request $request)
    {
        $trackId = $request->input('track_id');
        if (! $trackId) {
            return response()->json(['error' => 'Falta el ID'], 400);
        }

        try {
            Http::withoutVerifying();

            $track = Spotify::track($trackId)->get();

            $music = Music::query()->create([
                'title' => $track['name'],
                'artist' => $track['artists'][0]['name'],
                'cover_url' => $track['album']['images'][0]['url'] ?? '',
                'release_date' => $track['album']['release_date'],
            ]);

            return response()->json(['message' => '¡Canción añadida!', 'track' => $music], 201);
        } catch (Exception $exception) {
            return response()->json(['error' => 'Error al guardar: '.$exception->getMessage()], 500);
        }
    }
}
