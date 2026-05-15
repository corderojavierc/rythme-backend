<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\CommentResource;
use App\Http\Resources\ItemsLikedResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class UserController
{
    public function index(): AnonymousResourceCollection
    {
        try {
            $currentUserId = Auth::id();

            $users = User::query()
                ->where('id', '!=', $currentUserId)
                ->orderByRaw("
                    CASE type
                        WHEN 'artist' THEN 1
                        WHEN 'creator' THEN 2
                        WHEN 'admin' THEN 3
                        ELSE 4
                    END
                ")
                ->orderBy('username', 'asc')
                ->withExists(['followers as is_following_auth' => function (Builder $query) use ($currentUserId): void {
                    $query->where('follower_id', $currentUserId);
                }])
                ->paginate(10);

            return UserResource::collection($users);
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar los usuarios.');
        } catch (Exception) {
            abort(500, 'Error al cargar los usuarios.');
        }
    }

    public function search(Request $request): AnonymousResourceCollection
    {
        try {
            $request->validate(['text' => ['required', 'string']]);

            $query = $request->text;
            $currentUserId = Auth::id();

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
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al buscar usuarios.');
        } catch (Exception) {
            abort(500, 'Error al buscar usuarios.');
        }
    }

    public function getPosts(string $id): AnonymousResourceCollection
    {
        try {
            User::query()->findOrFail($id);

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
        } catch (ModelNotFoundException) {
            abort(404, 'Error: el usuario no ha sido encontrado.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar las publicaciones del usuario.');
        } catch (Exception) {
            abort(500, 'Error al cargar las publicaciones del usuario.');
        }
    }

    public function getComments(string $id): AnonymousResourceCollection
    {
        try {
            User::query()->findOrFail($id);

            $comments = Comment::with(['post', 'user'])
                ->withExists(['likes as is_liked' => function (Builder $query): void {
                    $query->where('user_id', Auth::id());
                }])
                ->where('user_id', $id)
                ->latest()
                ->paginate(20);

            return CommentResource::collection($comments);
        } catch (ModelNotFoundException) {
            abort(404, 'Error: el usuario no ha sido encontrado.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar los comentarios del usuario.');
        } catch (Exception) {
            abort(500, 'Error al cargar los comentarios del usuario.');
        }
    }

    public function getLiked(string $id): AnonymousResourceCollection
    {
        try {
            User::query()->findOrFail($id);

            $authUserId = (string) Auth::id();

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
        } catch (ModelNotFoundException) {
            abort(404, 'Error: el usuario no ha sido encontrado.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar los likes del usuario.');
        } catch (Exception) {
            abort(500, 'Error al cargar los likes del usuario.');
        }
    }

    public function me(): UserResource
    {
        try {
            $currentUserId = Auth::id();

            $user = User::query()
                ->where('id', $currentUserId)
                ->withExists(['followers as is_following_auth' => function (Builder $query) use ($currentUserId): void {
                    $query->where('follower_id', $currentUserId);
                }])
                ->firstOrFail();

            return new UserResource($user);
        } catch (ModelNotFoundException) {
            abort(401, 'Error: no se ha podido obtener la información de tu perfil o no estás autenticado.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al obtener tu perfil.');
        } catch (Exception) {
            abort(500, 'Error al obtener tu perfil.');
        }
    }

    public function show(string $username): UserResource
    {
        try {
            $currentUserId = Auth::id();

            $user = User::query()
                ->where('username', $username)
                ->withExists(['followers as is_following_auth' => function (Builder $query) use ($currentUserId): void {
                    $query->where('follower_id', $currentUserId);
                }])
                ->firstOrFail();

            return new UserResource($user);
        } catch (ModelNotFoundException) {
            abort(404, 'Error: el usuario no ha sido encontrado.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al obtener el perfil del usuario.');
        } catch (Exception) {
            abort(500, 'Error al obtener el perfil del usuario.');
        }
    }
}
