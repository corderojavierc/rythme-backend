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

// Gestiona perfiles de usuario y su contenido (posts, comentarios, likes)
final class UserController
{
    // Lista todos los usuarios excepto el autenticado, priorizando artistas y creadores
    public function index(): AnonymousResourceCollection
    {
        try {
            $currentUserId = Auth::id();

            $users = User::query()
                ->where('id', '!=', $currentUserId)
                // CASE en SQL para ordenar por rol: artistas primero, luego creadores, luego admins, luego usuarios.
                // Dentro de cada grupo, ordena alfabéticamente por username.
                ->orderByRaw("
                    CASE type
                        WHEN 'artist' THEN 1
                        WHEN 'creator' THEN 2
                        WHEN 'admin' THEN 3
                        ELSE 4
                    END
                ")
                ->orderBy('username', 'asc')
                // Añade el campo is_following_auth: true si el usuario autenticado ya sigue a este usuario.
                // Se hace en la misma query SQL para evitar N consultas extra (una por usuario).
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

    // Busca usuarios por username o nombre
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

    // Devuelve los posts de un usuario concreto
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

    // Devuelve los comentarios hechos por un usuario concreto
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

    // Devuelve todos los posts y comentarios que ha likeado un usuario
    public function getLiked(string $id): AnonymousResourceCollection
    {
        try {
            User::query()->findOrFail($id);

            $authUserId = (string) Auth::id();

            /** @var LengthAwarePaginator $likes */
            $likes = Like::query()
                ->where('user_id', $id)
                // 'likeable' es una relación polimórfica: un Like puede apuntar a un Post o a un Comment.
                // morphWith carga las relaciones adicionales según el tipo real del modelo.
                // Si es Post → carga music y user. Si es Comment → carga post y user.
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

            // Aquí procesamos manualmente cada like para añadir campos extra que el frontend necesita.
            // No se puede hacer con withExists porque el modelo puede ser Post o Comment (polimórfico).
            $items = $likes->getCollection()->map(function (Like $like) use ($authUserId): ?Model {
                /** @var Post|Comment|null $model */
                $model = $like->likeable;

                // Si el post o comentario fue borrado, likeable puede ser null. Lo saltamos.
                if ($model === null) {
                    return null;
                }

                // Añadimos is_liked: si el usuario AUTENTICADO (no el del perfil) ha dado like a este item.
                $model->setAttribute('is_liked', $model->likes()->where('user_id', $authUserId)->exists());

                // is_valorated solo tiene sentido para Posts (saber si el autenticado ya reseñó esa canción).
                if ($model instanceof Post) {
                    $model->setAttribute('is_valorated', $model->music()->whereHas('post', function (Builder $q) use ($authUserId): void {
                        $q->where('user_id', $authUserId);
                    })->exists());
                }

                return $model;
            })->filter(); // filter() elimina los null que dejamos pasar arriba

            // Reemplazamos la colección interna del paginador con los datos procesados
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

    // Devuelve el perfil del usuario autenticado
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

    // Devuelve el perfil de un usuario por su username
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
