<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\MusicResource;
use App\Http\Resources\PostResource;
use App\Models\Music;
use App\Models\Post;
use App\Services\SpotifyService;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
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

        $query = $data['name'];

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

    public function show(string $id): MusicResource
    {
        $music = Music::query()->findOrFail($id);

        return new MusicResource($music);
    }

    public function update(): JsonResponse
    {
        return response()->json([]);
    }

    public function destroy(): JsonResponse
    {
        return response()->json([]);
    }

    public function getPosts(string $id): AnonymousResourceCollection
    {
        $currentUserId = auth()->id();
        Music::query()->findOrFail($id);

        $posts = Post::query()->where('music_id', $id)
            ->withExists(['likes as is_liked' => function (Builder $query) use ($currentUserId): void {
                $query->where('user_id', $currentUserId);
            }])
            ->withExists(['music as is_valorated' => function (Builder $query) use ($currentUserId): void {
                $query->whereHas('post', function (Builder $pQuery) use ($currentUserId): void {
                    $pQuery->where('user_id', $currentUserId);
                });
            }])
            ->with(['user', 'music'])
            ->latest()
            ->paginate(10);

        return PostResource::collection($posts);
    }
}
