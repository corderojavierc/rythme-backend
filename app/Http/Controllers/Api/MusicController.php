<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\MusicResource;
use App\Http\Resources\PostResource;
use App\Models\Music;
use App\Models\Post;
use App\Models\User;
use App\Services\SpotifyService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Gestiona las canciones: búsqueda local y en Spotify, y posts asociados
final class MusicController
{
    public function index(): JsonResponse
    {
        return response()->json([]);
    }

    // Busca una canción en la BD local; si no existe, la busca y guarda desde Spotify
    public function store(Request $request): MusicResource
    {
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'min:2'],
            ]);

            $query = $data['name'];

            $music = Music::query()
                ->where('title', 'like', sprintf('%%%s%%', $query))
                ->orWhere('artist', 'like', sprintf('%%%s%%', $query))
                ->first();

            if (! $music) {
                $music = SpotifyService::searchAndStore($query);
            }

            abort_unless($music instanceof Music, 404, 'Error: la canción no ha sido encontrada en ninguna plataforma.');

            return new MusicResource($music);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al buscar o guardar la canción.');
        } catch (Exception $e) {
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error: ha ocurrido un problema al buscar o guardar la canción.');
        }
    }

    // Busca canciones por nombre: combina resultados locales con Spotify si hay pocos resultados
    public function search(Request $request): AnonymousResourceCollection
    {
        try {
            $request->validate(['name' => ['required', 'string', 'min:1']]);

            $query = $request->input('name');
            $perPage = 10;
            $page = $request->input('page', 1);

            // Primero busca en la BD propia (más rápido, sin petición externa)
            $localSongs = Music::query()
                ->where('title', 'like', sprintf('%%%s%%', $query))
                ->orWhere('artist', 'like', sprintf('%%%s%%', $query))
                ->get();

            $combined = $localSongs;

            // Si hay pocas canciones locales, complementa con Spotify para dar más resultados.
            // Si Spotify falla (sin conexión, límite de API...) simplemente seguimos con los locales.
            if ($localSongs->count() < 20) {
                try {
                    $spotifySongs = SpotifyService::searchInSpotify($query, 20);
                    // unique() evita que aparezca la misma canción dos veces (local + Spotify).
                    // Usa "título|artista" en minúsculas como clave de unicidad.
                    $combined = $localSongs->concat($spotifySongs)
                        ->unique(fn (Music $item): string => mb_strtolower($item->title.'|'.$item->artist));
                } catch (Exception) {
                }
            }

            // La combinación local+Spotify no es paginable directamente porque no viene de Eloquent.
            // forPage() hace el "slice" manual de la colección según la página pedida.
            $items = $combined->forPage($page, $perPage)->values();

            // Construimos manualmente el objeto paginador que espera Laravel/el frontend.
            // Necesita: los items de esta página, el total, el tamaño de página, la página actual y la URL base.
            $paginatedResults = new LengthAwarePaginator(
                $items,
                $combined->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return MusicResource::collection($paginatedResults);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al buscar canciones.');
        } catch (Exception $e) {
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error: no se ha podido realizar la búsqueda de canciones.');
        }
    }

    public function show(string $id): MusicResource
    {
        try {
            $music = Music::query()->findOrFail($id);

            return new MusicResource($music);
        } catch (ModelNotFoundException) {
            abort(404, 'Error: la canción no ha sido encontrada.');
        } catch (Exception) {
            abort(500, 'Error al obtener los detalles de la canción.');
        }
    }

    public function update(): JsonResponse
    {
        return response()->json([]);
    }

    public function destroy(): JsonResponse
    {
        return response()->json([]);
    }

    // Devuelve todos los posts que reseñan una canción concreta
    public function getPosts(string $id): AnonymousResourceCollection
    {
        try {
            $currentUserId = Auth::id();

            Music::query()->findOrFail($id);

            $posts = Post::query()
                ->where('music_id', $id)
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
        } catch (ModelNotFoundException) {
            abort(404, 'Error: la canción no ha sido encontrada.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar las publicaciones de la canción.');
        } catch (Exception) {
            abort(500, 'Error al cargar las publicaciones de la canción.');
        }
    }

    // Devuelve las canciones creadas por un artista verificado
    public function getUserMusics(string $id): AnonymousResourceCollection
    {
        try {
            $user = User::query()->findOrFail($id);

            abort_unless($user->isArtist(), 403, 'Error: este usuario no es un artista.');

            $musics = $user->createdMusic()->with('createdBy')->paginate(10);

            return MusicResource::collection($musics);
        } catch (ModelNotFoundException) {
            abort(404, 'Error: el artista no ha sido encontrado.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar las canciones del artista.');
        } catch (Exception $e) {
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error al cargar las canciones del artista.');
        }
    }
}
