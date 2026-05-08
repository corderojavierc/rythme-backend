<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\RankingResource;
use App\Models\MostValoratedMusic;
use App\Models\MusicRating;
use App\Models\Post;
use App\Models\TopRatedMusic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class RankingController
{
    private const int TOP = 10;

    public function getGeneralTopRated(): AnonymousResourceCollection
    {
        $rankings = MusicRating::query()
            ->orderByDesc('rating')
            ->with('music')
            ->limit(self::TOP)
            ->get()
            ->values()
            ->each(function ($item, $index) {
                $item->position = $index + 1;
            });

        return RankingResource::collection($rankings);
    }

    public function getGeneralMostRated(): AnonymousResourceCollection
    {
        $rankings = MusicRating::query()
            ->orderByDesc('count_ratings')
            ->with('music')
            ->limit(self::TOP)
            ->get()
            ->values()
            ->each(function ($item, $index) {
                $item->position = $index + 1;
            });

        return RankingResource::collection($rankings);
    }

    public function getTopRated(): JsonResponse
    {
        $period = now()->format('Y-m');
        $from = now()->startOfMonth()->startOfDay();
        $to = now()->endOfDay();

        $rankings = Cache::remember(
            key: 'rankings:top-rated:'.$period,
            ttl: now()->addHours(6),
            callback: fn (): Collection => Post::query()
                ->whereBetween('created_at', [$from, $to])
                ->selectRaw('music_id, AVG(rating) as rating, COUNT(*) as count_ratings')
                ->groupBy('music_id')
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
    }

    public function getMostRated(): JsonResponse
    {
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
    }

    public function getTopRatedHistory(string $period): JsonResponse
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
            return response()->json([
                'message' => 'Formato inválido. Usa YYYY-MM, ejemplo: 2025-04',
            ], 422);
        }

        if ($period === now()->format('Y-m')) {
            return $this->getTopRated();
        }

        $date = now()->createFromFormat('Y-m', $period)->startOfMonth();

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
    }

    public function getMostRatedHistory(string $period): JsonResponse
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
            return response()->json([
                'message' => 'Formato inválido. Usa YYYY-MM, ejemplo: 2025-04',
            ], 422);
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
    }
}
