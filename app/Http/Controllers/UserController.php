<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\ItemsLikedResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

final class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::query()->paginate(60);

        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): void
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): void
    {
        //
    }

    public function getPosts(string $id): AnonymousResourceCollection
    {
        $posts = Post::with(['music', 'user'])
            ->withExists(['likes as is_liked' => function (Builder $query) use ($id): void {
                $query->where('user_id', $id);
            }])
            ->withExists(['music as is_valorated' => function (Builder $query) use ($id): void {
                $query->whereHas('post', function (Builder $pQuery) use ($id): void {
                    $pQuery->where('user_id', $id);
                });
            }])
            ->where('user_id', $id)
            ->latest()
            ->paginate(15);

        return PostResource::collection($posts);
    }

    public function getComments(string $id): AnonymousResourceCollection
    {
        $comments = Comment::with(['post', 'user'])
            ->withExists(['likes as is_liked' => function (Builder $query): void {
                $query->where('user_id', auth()->id());
            }])
            ->where('user_id', $id)
            ->latest()
            ->paginate(20);

        return CommentResource::collection($comments);
    }

    public function getLiked(string $id): AnonymousResourceCollection
    {
        /** @var LengthAwarePaginator $likes */
        $likes = Like::query()
            ->where('user_id', $id)
            ->with(['likeable' => function (Relation $query): void {
                if ($query instanceof MorphTo) {
                    $query->morphWith([
                        Post::class => ['music', 'user'],
                        Comment::class => ['post', 'user'],
                    ]);
                }
            }])
            ->latest()
            ->paginate(15);

        $items = $likes->getCollection()->map(function (Like $like) use ($id): ?Model {
            /** @var Post|Comment|null $model */
            $model = $like->likeable;

            if ($model === null) {
                return null;
            }

            $model->setAttribute('is_liked', true);

            if ($model instanceof Post) {
                $model->setAttribute('is_valorated', $model->music()->whereHas('post', function (Builder $q) use ($id): void {
                    $q->where('user_id', $id);
                })->exists());
            }

            return $model;
        })->filter();

        $likes->setCollection($items);

        return ItemsLikedResource::collection($likes);
    }
}
