<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Gestiona las publicaciones (reseñas de canciones) de la red social
final class PostController
{
    // Devuelve todos los posts paginados, incluyendo si el usuario los ha dado like o ya valoró esa canción
    public function index(): AnonymousResourceCollection
    {
        try {
            $currentUserId = Auth::id();

            $posts = Post::with(['music', 'user'])
                // withExists añade un campo booleano calculado en la misma query de BD.
                // 'likes as is_liked' → el campo se llamará is_liked en el resultado.
                // Comprueba si existe algún like de este usuario concreto en este post.
                ->withExists(['likes as is_liked' => function (Builder $query) use ($currentUserId): void {
                    $query->where('user_id', $currentUserId);
                }])
                // Comprueba si este usuario ya ha escrito un post (reseña) para la canción del post.
                // Así el frontend sabe si mostrar "ya valoraste esta canción".
                ->withExists(['music as is_valorated' => function (Builder $query) use ($currentUserId): void {
                    $query->whereHas('post', function (Builder $pQuery) use ($currentUserId): void {
                        $pQuery->where('user_id', $currentUserId);
                    });
                }])
                ->latest()   // ordena por created_at DESC (los más nuevos primero)
                ->paginate(15);

            return PostResource::collection($posts);
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar las publicaciones.');
        } catch (Exception) {
            abort(500, 'Error al cargar las publicaciones.');
        }
    }

    // Crea un post (reseña de canción). Impide que el mismo usuario valore la misma canción dos veces
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'music_id' => ['required', 'exists:musics,id'],
                'text' => ['required', 'string'],
                'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            ]);

            $exists = Post::query()
                ->where('user_id', Auth::id())
                ->where('music_id', $data['music_id'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'music_id' => 'Error: ya has valorado esta canción anteriormente.',
                ]);
            }

            $post = Post::query()->create([
                'user_id' => Auth::id(),
                'music_id' => $data['music_id'],
                'text' => $data['text'],
                'rating' => $data['rating'],
                'count_likes' => 0,
                'count_comments' => 0,
            ]);

            return response()->json([
                'message' => 'Publicación creada correctamente.',
                'post' => new PostResource($post),
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al crear la publicación.');
        } catch (Exception) {
            abort(500, 'Error: no se ha podido crear la publicación.');
        }
    }

    // Devuelve un post concreto por ID
    public function show(string $id): PostResource
    {
        try {
            /** @var Post $post */
            $post = Post::with(['music', 'user'])
                ->withExists(['likes as is_liked' => function (Builder $query): void {
                    $query->where('user_id', Auth::id());
                }])
                ->withExists(['music as is_valorated' => function (Builder $query): void {
                    $query->whereHas('post', function (Builder $pQuery): void {
                        $pQuery->where('user_id', Auth::id());
                    });
                }])
                ->findOrFail($id);

            return new PostResource($post);
        } catch (ModelNotFoundException) {
            abort(404, 'Error: la publicación no ha sido encontrada.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al obtener la publicación.');
        } catch (Exception) {
            abort(500, 'Error al obtener la publicación.');
        }
    }

    public function update(): JsonResponse
    {
        return response()->json([]);
    }

    // Elimina un post; solo lo puede hacer su autor
    public function destroy(string $id): JsonResponse
    {
        try {
            /** @var Post $post */
            $post = Post::query()->findOrFail($id);

            // abort_if detiene la ejecución con 403 Forbidden si el post no pertenece al usuario.
            // Así evitamos que alguien borre posts ajenos aunque conozca el ID.
            abort_if($post->user_id !== Auth::id(), 403, 'Error: no tienes permisos para eliminar esta publicación.');

            $post->delete();

            return response()->json([
                'message' => 'Publicación eliminada correctamente.',
            ]);
        } catch (ModelNotFoundException) {
            abort(404, 'Error: la publicación no ha sido encontrada.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al eliminar la publicación.');
        } catch (Exception $e) {
            // throw_if re-lanza la excepción si es del tipo HttpException (nuestros abort()).
            // Sin esto, el catch genérico absorbería los 403/404 que acabamos de lanzar.
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error al eliminar la publicación.');
        }
    }

    // Busca posts por texto, username, nombre de usuario, título o artista de la canción
    public function search(Request $request): AnonymousResourceCollection
    {
        try {
            $request->validate(['text' => ['nullable', 'string']]);

            $query = $request->input('text');
            $currentUserId = Auth::id();

            $posts = Post::query()
                ->with(['music.rating', 'user'])
                ->withExists(['likes as is_liked' => function (Builder $query) use ($currentUserId): void {
                    $query->where('user_id', $currentUserId);
                }])
                ->withExists(['music as is_valorated' => function (Builder $query) use ($currentUserId): void {
                    $query->whereHas('post', function (Builder $pQuery) use ($currentUserId): void {
                        $pQuery->where('user_id', $currentUserId);
                    });
                }])
                // ->when() solo aplica el bloque si $query no está vacío.
                // Así, sin texto de búsqueda, devuelve todos los posts sin filtrar.
                ->when($query, function (Builder $q) use ($query): void {
                    // Agrupa las condiciones OR en un WHERE (...) para no mezclarlas con otros filtros.
                    // sprintf('%%%s%%', $query) genera "%texto%" para el operador LIKE de SQL.
                    $q->where(function (Builder $subQuery) use ($query): void {
                        $subQuery->where('text', 'like', sprintf('%%%s%%', $query))
                            // orWhereHas busca en la relación 'user' del post
                            ->orWhereHas('user', function (Builder $u) use ($query): void {
                                $u->where('username', 'like', sprintf('%%%s%%', $query))
                                    ->orWhere('name', 'like', sprintf('%%%s%%', $query));
                            })
                            // orWhereHas busca en la relación 'music' del post
                            ->orWhereHas('music', function (Builder $m) use ($query): void {
                                $m->where('title', 'like', sprintf('%%%s%%', $query))
                                    ->orWhere('artist', 'like', sprintf('%%%s%%', $query));
                            });
                    });

                    // Prioridad en el orden: posts cuyo propio texto coincide aparecen primero (CASE=1),
                    // los que coinciden solo por usuario o canción aparecen después (CASE=2).
                    $q->orderByRaw('CASE
                        WHEN text LIKE ? THEN 1
                        ELSE 2
                        END', [sprintf('%%%s%%', $query)]);
                })
                ->latest()
                ->paginate(10);

            return PostResource::collection($posts);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al buscar publicaciones.');
        } catch (Exception) {
            abort(500, 'Error al buscar publicaciones.');
        }
    }

    // Comprueba si el usuario ya tiene un post para una canción concreta
    public function checkPost(Request $request): JsonResponse
    {
        try {
            $request->merge(['music_id' => $request->route('music_id')]);
            $request->validate([
                'music_id' => ['required', 'exists:musics,id'],
            ]);

            $exists = Post::query()
                ->where('user_id', Auth::id())
                ->where('music_id', $request->music_id)
                ->exists();

            return response()->json([
                'exists' => $exists,
                'message' => $exists ? 'Ya has valorado esta canción.' : 'Disponible para valorar.',
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al comprobar la valoración.');
        } catch (Exception) {
            abort(500, 'Error al comprobar si ya has valorado esta canción.');
        }
    }

    // Devuelve solo los posts de usuarios que sigue el usuario autenticado
    public function getFollowedPosts(): AnonymousResourceCollection
    {
        try {
            $currentUserId = (string) Auth::id();

            /** @var LengthAwarePaginator $posts */
            $posts = Post::query()
                ->with(['music', 'user'])
                ->withExists(['likes as is_liked' => function (Builder $query) use ($currentUserId): void {
                    $query->where('user_id', $currentUserId);
                }])
                ->withExists(['music as is_valorated' => function (Builder $query) use ($currentUserId): void {
                    $query->whereHas('post', function (Builder $pQuery) use ($currentUserId): void {
                        $pQuery->where('user_id', $currentUserId);
                    });
                }])
                // whereIn con subconsulta: filtra solo los posts cuyos autores están en la tabla 'follows'
                // como usuarios seguidos por el usuario actual. Es más eficiente que cargar todos y filtrar en PHP.
                ->whereIn('user_id', function (QueryBuilder $query) use ($currentUserId): void {
                    $query->select('followed_id')
                        ->from('follows')
                        ->where('follower_id', $currentUserId);
                })
                ->latest()
                ->paginate(15);

            return PostResource::collection($posts);
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar las publicaciones de usuarios seguidos.');
        } catch (Exception) {
            abort(500, 'Error al cargar las publicaciones de usuarios seguidos.');
        }
    }
}
