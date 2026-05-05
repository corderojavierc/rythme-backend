<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\ItemsLikedResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

final class UserController
{
    public function index(): AnonymousResourceCollection
    {
        $currentUserId = auth()->id();

        $users = User::query()
            ->where('id', '!=', $currentUserId)
            ->withExists(['followers as is_following_auth' => function (Builder $query) use ($currentUserId): void {
                $query->where('follower_id', $currentUserId);
            }])
            ->paginate(10);

        return UserResource::collection($users);
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        $request->validate(['text' => ['required', 'string']]);

        $query = $request->text;
        $currentUserId = auth()->id();

        $users = User::query()
            ->where(function (Builder $q) use ($query): void {
                $q->where('username', 'like', sprintf('%%%s%%', $query))
                    ->orWhere('name', 'like', sprintf('%%%s%%', $query));
            })
            ->orderByRaw("
                CASE type
                    WHEN 'artist' THEN 1
                    WHEN 'creator' THEN 2
                    WHEN 'admin' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('username', 'asc')
            ->withExists(['followers as is_following_auth' => function (Builder $q) use ($currentUserId): void {
                $q->where('follower_id', $currentUserId);
            }])
            ->paginate(10);

        return UserResource::collection($users);
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
        $authUserId = (string) auth()->id();

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

        $items = $likes->getCollection()->map(function (Like $like) use ($authUserId): ?Model {
            /** @var Post|Comment|null $model */
            $model = $like->likeable;

            if ($model === null) {
                return null;
            }

            $model->setAttribute('is_liked', $model->likes()->where('user_id', $authUserId)->exists());

            if ($model instanceof Post) {
                $model->setAttribute('is_valorated', $model->music()->whereHas('post', function (Builder $q) use ($authUserId): void {
                    $q->where('user_id', $authUserId);
                })->exists());
            }

            return $model;
        })->filter();

        $likes->setCollection($items);

        return ItemsLikedResource::collection($likes);
    }

    public function me(): UserResource
    {
        $currentUserId = auth()->id();

        $user = User::query()
            ->where('id', $currentUserId)
            ->withExists(['followers as is_following_auth' => function (Builder $query) use ($currentUserId): void {
                $query->where('follower_id', $currentUserId);
            }])
            ->firstOrFail();

        return new UserResource($user);
    }
}
