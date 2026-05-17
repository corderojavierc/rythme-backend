<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\RankingResource;
use App\Models\MostValoratedMusic;
use App\Models\MusicRating;
use App\Models\Post;
use App\Models\TopRatedMusic;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

// Calcula y devuelve los rankings de canciones. Usa caché para no recalcular en cada petición
final class RankingController
{
    private const int TOP = 10;

    // Top 10 canciones mejor valoradas de todos los tiempos (por nota media)
    public function getGeneralTopRated(): AnonymousResourceCollection
    {
        try {
            $rankings = MusicRating::query()
                ->orderByDesc('rating')
                ->with('music')
                ->limit(self::TOP)
                ->get()
                ->values()
                ->each(function (MusicRating $item, int $index): void {
                    $item->setAttribute('position', $index + 1);
                });

            return RankingResource::collection($rankings);
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar el ranking general de mejor valorados.');
        } catch (Exception) {
            abort(500, 'Error al cargar el ranking general de mejor valorados.');
        }
    }

    // Top 10 canciones con más valoraciones de todos los tiempos (por cantidad)
    public function getGeneralMostRated(): AnonymousResourceCollection
    {
        try {
            $rankings = MusicRating::query()
                ->orderByDesc('count_ratings')
                ->with('music')
                ->limit(self::TOP)
                ->get()
                ->values()
                ->each(function (MusicRating $item, int $index): void {
                    $item->setAttribute('position', $index + 1);
                });

            return RankingResource::collection($rankings);
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar el ranking general de más valorados.');
        } catch (Exception) {
            abort(500, 'Error al cargar el ranking general de más valorados.');
        }
    }

    // Top 10 del mes actual por nota media; resultado cacheado 6 horas
    public function getTopRated(): JsonResponse
    {
        try {
            $period = now()->format('Y-m');
            $from = now()->startOfMonth()->startOfDay();
            $to = now()->endOfDay();

            // Cache::remember ejecuta el callback solo si la clave no existe en caché.
            // Si ya existe, devuelve directamente el valor guardado sin tocar la BD.
            // TTL de 6 horas: el ranking se recalcula máximo 4 veces al día.
            $rankings = Cache::remember(
                key: 'rankings:top-rated:'.$period,
                ttl: now()->addHours(6),
                callback: fn (): Collection => Post::query()
                    ->whereBetween('created_at', [$from, $to])
                    // selectRaw agrupa todos los posts de cada canción en una sola fila,
                    // calculando la media de rating y cuántas reseñas tiene.
                    ->selectRaw('music_id, AVG(rating) as rating, COUNT(*) as count_ratings')
                    ->groupBy('music_id')
                    ->orderByDesc('rating')
                    ->with('music')
                    ->limit(self::TOP)
                    ->get()
                    // map recorre la colección y añade el número de posición (1, 2, 3...) a cada item
                    ->map(fn (Post $item, int $i): Post => $item->setAttribute('position', $i + 1))
            );

            return response()->json([
                'period' => $period,
                'data' => RankingResource::collection($rankings),
            ]);
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar el ranking de mejor valorados del mes.');
        } catch (Exception) {
            abort(500, 'Error al cargar el ranking de mejor valorados del mes.');
        }
    }

    // Top 10 del mes actual por número de reseñas; resultado cacheado 6 horas
    public function getMostRated(): JsonResponse
    {
        try {
            $period = now()->format('Y-m');
            $from = now()->startOfMonth()->startOfDay();
            $to = now()->endOfDay();

            $rankings = Cache::remember(
                key: 'rankings:most-rated:'.$period,
                ttl: now()->addHours(6),
                callback: fn (): Collection => Post::query()
                    ->whereBetween('created_at', [$from, $to])
                    ->selectRaw('music_id, COUNT(*) as count_ratings, AVG(rating) as rating')
                    ->groupBy('music_id')
                    ->orderByDesc('count_ratings')
                    ->orderByDesc('rating')
                    ->with('music')
                    ->limit(self::TOP)
                    ->get()
                    ->map(fn (Post $item, int $i): Post => $item->setAttribute('position', $i + 1))
            );

            return response()->json([
                'period' => $period,
                'data' => RankingResource::collection($rankings),
            ]);
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar el ranking de más valorados del mes.');
        } catch (Exception) {
            abort(500, 'Error al cargar el ranking de más valorados del mes.');
        }
    }

    // Ranking histórico por nota media de un mes pasado (formato YYYY-MM); cacheado para siempre porque ya no cambia
    public function getTopRatedHistory(string $period): JsonResponse
    {
        try {
            // Valida que el formato sea exactamente YYYY-MM (ej: 2025-04)
            if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
                throw ValidationException::withMessages([
                    'period' => 'Error: formato de fecha inválido. Usa YYYY-MM (ej: 2025-04).',
                ]);
            }

            // Si piden el mes actual, redirigimos al método en vivo (que usa datos en tiempo real)
            if ($period === now()->format('Y-m')) {
                return $this->getTopRated();
            }

            $date = now()->createFromFormat('Y-m', $period)->startOfMonth();

            // rememberForever: los meses pasados nunca cambian, así que no hace falta expirar la caché.
            // Los datos vienen de la tabla top_rated_musics, llenada por SnapshotMonthlyRankingJob.
            $rankings = Cache::rememberForever(
                key: 'rankings:top-rated:history:'.$period,
                callback: fn (): Collection => TopRatedMusic::query()
                    ->with('music')
                    ->where('period', $date->toDateString())
                    ->orderBy('rank_position')
                    ->get()
            );

            return response()->json([
                'period' => $period,
                'data' => RankingResource::collection($rankings),
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar el historial de mejor valorados.');
        } catch (Exception) {
            abort(500, 'Error: no se ha podido cargar el historial de mejores valoraciones.');
        }
    }

    // Ranking histórico por cantidad de reseñas de un mes pasado; cacheado para siempre porque ya no cambia
    public function getMostRatedHistory(string $period): JsonResponse
    {
        try {
            if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
                throw ValidationException::withMessages([
                    'period' => 'Error: formato de fecha inválido. Usa YYYY-MM (ej: 2025-04).',
                ]);
            }

            if ($period === now()->format('Y-m')) {
                return $this->getMostRated();
            }

            $date = now()->createFromFormat('Y-m', $period)->startOfMonth();

            $rankings = Cache::rememberForever(
                key: 'rankings:most-rated:history:'.$period,
                callback: fn (): Collection => MostValoratedMusic::query()
                    ->with('music')
                    ->where('period', $date->toDateString())
                    ->orderBy('rank_position')
                    ->get()
            );

            return response()->json([
                'period' => $period,
                'data' => RankingResource::collection($rankings),
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al cargar el historial de más valorados.');
        } catch (Exception) {
            abort(500, 'Error: no se ha podido cargar el historial de los más valorados.');
        }
    }
}
