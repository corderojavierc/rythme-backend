<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\FollowResource;
use App\Models\Follow;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class FollowController
{
    public function index(string $id): AnonymousResourceCollection
    {
        $follows = Follow::query()
            ->with(['follower', 'followed'])
            ->where('follower_id', $id)
            ->get();

        return FollowResource::collection($follows);
    }

    public function store(Request $request): JsonResponse
    {
        /** @var array{follower_id: string, followed_id: string} $validated */
        $validated = $request->validate([
            'follower_id' => ['required', 'exists:users,id'],
            'followed_id' => ['required', 'exists:users,id'],
        ]);

        if ($validated['follower_id'] === $validated['followed_id']) {
            return response()->json(['message' => 'invalid'], 422);
        }

        $exists = Follow::query()->where($validated)->exists();

        if ($exists) {
            return response()->json(['message' => 'already following'], 409);
        }

        Follow::query()->create($validated);

        return response()->json(true, 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        /** @var array{follower_id: string, followed_id: string} $validated */
        $validated = $request->validate([
            'follower_id' => ['required', 'exists:users,id'],
            'followed_id' => ['required', 'exists:users,id'],
        ]);

        Follow::query()->where($validated)->delete();

        return response()->json(true, 204);
    }
}
