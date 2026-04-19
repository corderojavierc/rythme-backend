<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\MusicResource;
use App\Models\Music;
use App\Services\SpotifyService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class MusicController
{
    public function index(): JsonResponse
    {
        return response()->json([]);
    }

    public function store(Request $request): MusicResource|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2'],
        ]);

        /** @var string $query */
        $query = $data['name'];

        /** @var Music|null $music */
        $music = Music::query()->where('title', 'like', sprintf('%%%s%%', $query))
            ->orWhere('artist', 'like', sprintf('%%%s%%', $query))
            ->first();

        if (! $music) {
            $music = SpotifyService::searchAndStore($query);
        }

        if (! $music) {
            return response()->json(['message' => 'La canción no fue encontrada en ninguna plataforma.'], 404);
        }

        return new MusicResource($music);
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2'],
        ]);

        /** @var string $query */
        $query = $data['name'];
        $limit = 5;

        $localSongs = Music::query()->where('title', 'like', sprintf('%%%s%%', $query))
            ->orWhere('artist', 'like', sprintf('%%%s%%', $query))
            ->limit($limit)
            ->get();

        if ($localSongs->count() >= $limit) {
            return MusicResource::collection($localSongs)->additional(['source' => 'local']);
        }

        try {
            $spotifySongs = SpotifyService::searchInSpotify($query, 10);

            $combined = $localSongs->concat($spotifySongs)
                ->unique(fn (Music $item): string => mb_strtolower($item->title.'|'.$item->artist))
                ->take($limit)
                ->values();

            return MusicResource::collection($combined)->additional([
                'source' => $localSongs->count() > 0 ? 'mixed' : 'external',
                'count' => $combined->count(),
            ]);

        } catch (Exception) {
            return MusicResource::collection($localSongs);
        }
    }

    public function show(): JsonResponse
    {
        return response()->json([]);
    }

    public function update(): JsonResponse
    {
        return response()->json([]);
    }

    public function destroy(): JsonResponse
    {
        return response()->json([]);
    }
}
