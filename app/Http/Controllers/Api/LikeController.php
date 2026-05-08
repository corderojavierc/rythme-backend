<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\LikeResource;
use App\Models\Like;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class LikeController
{
    public function index(string $id): AnonymousResourceCollection
    {
        $likes = Like::query()
            ->with(['user', 'likeable'])
            ->where('user_id', $id)
            ->get();

        return LikeResource::collection($likes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'likeable_type' => ['required', 'string'],
            'likeable_id' => ['required', 'string'],
        ]);

        Like::query()->create($validated);

        return response()->json(true, 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'likeable_type' => ['required', 'string'],
            'likeable_id' => ['required', 'string'],
        ]);

        $like = Like::query()->where($validated)->first();

        if ($like) {
            $like->delete();
        }

        return response()->json(true, 204);
    }
}
