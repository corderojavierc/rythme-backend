<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\LikeResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
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
        /** @var array{user_id: string, likeable_type: string, likeable_id: string} $validated */
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'likeable_type' => ['required', 'string'],
            'likeable_id' => ['required', 'string'],
        ]);

        Like::query()->create($validated);

        $this->incrementLikeable($validated['likeable_type'], $validated['likeable_id']);

        return response()->json(true, 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        /** @var array{user_id: string, likeable_type: string, likeable_id: string} $validated */
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'likeable_type' => ['required', 'string'],
            'likeable_id' => ['required', 'string'],
        ]);

        Like::query()->where($validated)->delete();

        $this->decrementLikeable($validated['likeable_type'], $validated['likeable_id']);

        return response()->json(true, 204);
    }

    private function incrementLikeable(string $type, string $id): void
    {
        if ($type === Post::class) {
            $post = Post::query()->find($id);

            if ($post) {
                $post->increment('count_likes');
            }
        }

        if ($type === Comment::class) {
            $comment = Comment::query()->find($id);

            if ($comment) {
                $comment->increment('count_likes');
            }
        }
    }

    private function decrementLikeable(string $type, string $id): void
    {
        if ($type === Post::class) {
            $post = Post::query()->find($id);

            if ($post) {
                $post->decrement('count_likes');
            }
        }

        if ($type === Comment::class) {
            $comment = Comment::query()->find($id);

            if ($comment) {
                $comment->decrement('count_likes');
            }
        }
    }
}
