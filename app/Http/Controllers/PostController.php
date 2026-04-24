<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

final class PostController
{
    /**
     * Display a listing of the resource.
     */
     public function index(): AnonymousResourceCollection
     {
         $currentUserId = auth()->id();

         $posts = Post::with(['music', 'user'])
             ->withExists(['likes as is_liked' => function (Builder $query) use ($currentUserId): void {
                 $query->where('user_id', $currentUserId);
             }])
             ->withExists(['music as is_valorated' => function (Builder $query) use ($currentUserId): void {
                 $query->whereHas('post', function (Builder $pQuery) use ($currentUserId) {
                     $pQuery->where('user_id', $currentUserId);
                 });
             }])
             ->latest()
             ->paginate(15);

         return PostResource::collection($posts);
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'music_id' => ['required', 'exists:musics,id'],
                'text' => ['required', 'string'],
                'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            ]);

            $exists = Post::query()->where('user_id', auth()->id())
                ->where('music_id', $data['music_id'])
                ->exists();
            if ($exists) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => ['music_id' => ['Ya has valorado esta canción anteriormente.']],
                ], 422);
            }

            $post = Post::query()->create([
                'user_id' => auth()->id(),
                'music_id' => $data['music_id'],
                'text' => $data['text'],
                'rating' => $data['rating'],
                'count_likes' => 0,
                'count_comments' => 0,
            ]);

            return response()->json([
                'message' => 'Post created successfully',
                'post' => new PostResource($post),
            ], 201);
        } catch (ValidationException $validationException) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validationException->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse|PostResource
    {
        try {
            /** @var Post $post */
            $post = Post::with(['music', 'user'])
                ->withExists(['likes as is_liked' => function (Builder $query): void {
                    $query->where('user_id', auth()->id());
                }])
                ->findOrFail($id);

            return new PostResource($post);
        } catch (Exception) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(): JsonResponse
    {
        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            /** @var Post $post */
            $post = Post::query()->findOrFail($id);

            $post->delete();

            return response()->json([
                'message' => 'Post deleted successfully',
            ]);
        } catch (Exception) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }
    }

    public function checkPost(Request $request): JsonResponse
    {
        $exists = Post::query()->where('user_id', auth()->id())
            ->where('music_id', $request->music_id)
            ->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Ya has valorado esta canción.' : 'Disponible para valorar.',
        ]);
    }

    public function getFollowedPosts(): AnonymousResourceCollection
    {
        $currentUserId = auth()->id();

        $posts = Post::query()
            ->with(['music', 'user'])
            ->withExists(['likes as is_liked' => function (Builder $query) use ($currentUserId): void {
                $query->where('user_id', $currentUserId);
            }])
            ->withExists(['music as is_valorated' => function (Builder $query) use ($currentUserId): void {
                $query->whereHas('post', function (Builder $pQuery) use ($currentUserId) {
                    $pQuery->where('user_id', $currentUserId);
                });
            }])
            ->whereIn('user_id', function ($query) use ($currentUserId): void {
                $query->select('followed_id')
                    ->from('follows')
                    ->where('follower_id', $currentUserId);
            })
            ->latest()
            ->paginate(15);

        return PostResource::collection($posts);
    }
}
