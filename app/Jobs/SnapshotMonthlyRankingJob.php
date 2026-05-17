<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\MostValoratedMusic;
use App\Models\Post;
use App\Models\TopRatedMusic;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

// Job que se ejecuta una vez al mes para guardar permanentemente el ranking del mes que acaba de terminar
final class SnapshotMonthlyRankingJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        private readonly ?CarbonInterface $snapshotMonth = null,
    ) {}

    public function uniqueId(): string
    {
        return $this->resolveMonth()->format('Y-m');
    }

    // Calcula el top 10 del mes y lo guarda en las tablas de historial; limpia la caché al terminar
    public function handle(): void
    {
        try {
            $month = $this->resolveMonth();
            $from = $month->copy()->startOfMonth()->startOfDay();
            $to = $month->copy()->endOfMonth()->endOfDay();
            $period = $from->toDateString();

            // DB::transaction garantiza que los dos snapshots (topRated y mostRated)
            // se guardan juntos o ninguno. Si uno falla, el otro se deshace (rollback).
            DB::transaction(function () use ($from, $to, $period): void {
                $this->snapshotTopRated($from, $to, $period);
                $this->snapshotMostRated($from, $to, $period);
            });

            // Limpia la caché del mes procesado para que las siguientes peticiones
            // lean ya los datos históricos recién guardados en lugar del ranking en vivo.
            $cacheKey = $month->format('Y-m');
            Cache::forget('rankings:top-rated:'.$cacheKey);
            Cache::forget('rankings:most-rated:'.$cacheKey);
            Cache::forget('rankings:top-rated:history:'.$cacheKey);
            Cache::forget('rankings:most-rated:history:'.$cacheKey);
        } catch (Throwable $throwable) {
            Log::error('Error en SnapshotMonthlyRankingJob: '.$throwable->getMessage(), [
                'month' => $this->resolveMonth()->format('Y-m'),
                'exception' => $throwable,
            ]);

            throw $throwable;
        }
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('SnapshotMonthlyRankingJob falló definitivamente: '.$exception->getMessage(), [
            'month' => $this->resolveMonth()->format('Y-m'),
        ]);
    }

    private function resolveMonth(): CarbonInterface
    {
        return $this->snapshotMonth ?? now()->subMonth();
    }

    // Guarda el top 10 por nota media del período dado
    private function snapshotTopRated(CarbonInterface $from, CarbonInterface $to, string $period): void
    {
        $rows = Post::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('music_id, AVG(rating) as rating, COUNT(*) as count_ratings')
            ->groupBy('music_id')
            ->orderByDesc('rating')
            ->limit(10)
            ->get()
            // Convertimos cada resultado en un array plano listo para insertar en la BD
            ->map(fn (Post $item, int $i): array => [
                'period' => $period,
                'rank_position' => $i + 1,       // posición 1 a 10
                'music_id' => $item->music_id,
                'rating' => round((float) $item->rating, 2),
                'count_ratings' => (int) $item->count_ratings,
            ])
            ->all();

        if (! empty($rows)) {
            // upsert inserta las filas y, si ya existe una con la misma combinación de
            // period + rank_position, actualiza los demás campos en lugar de fallar.
            // Útil si el job se ejecuta dos veces por error: no duplica datos.
            TopRatedMusic::query()->upsert(
                $rows,
                uniqueBy: ['period', 'rank_position'],
                update: ['music_id', 'rating', 'count_ratings']
            );
        }
    }

    // Guarda el top 10 por número de reseñas del período dado
    private function snapshotMostRated(CarbonInterface $from, CarbonInterface $to, string $period): void
    {
        $rows = Post::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('music_id, COUNT(*) as count_ratings, AVG(rating) as rating')
            ->groupBy('music_id')
            ->orderByDesc('count_ratings')
            ->orderByDesc('rating')
            ->limit(10)
            ->get()
            ->map(fn (Post $item, int $i): array => [
                'period' => $period,
                'rank_position' => $i + 1,
                'music_id' => $item->music_id,
                'count_ratings' => (int) $item->count_ratings,
                'rating' => round((float) $item->rating, 2),
            ])
            ->all();

        if (! empty($rows)) {
            MostValoratedMusic::query()->upsert(
                $rows,
                uniqueBy: ['period', 'rank_position'],
                update: ['music_id', 'count_ratings', 'rating']
            );
        }
    }
}
