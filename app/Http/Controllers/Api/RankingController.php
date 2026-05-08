<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\RankingResource;
use App\Models\MostValoratedMusic;
use App\Models\Post;
use App\Models\TopRatedMusic;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class RankingController
{
    private const int TOP = 10;

    public function topRated(): JsonResponse
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

    public function mostRated(): JsonResponse
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

    public function topRatedHistory(string $period): JsonResponse
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
            return response()->json([
                'message' => 'Formato inválido. Usa YYYY-MM, ejemplo: 2025-04',
            ], 422);
        }

        if ($period === now()->format('Y-m')) {
            return $this->topRated();
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

    public function mostRatedHistory(string $period): JsonResponse
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $period)) {
            return response()->json([
                'message' => 'Formato inválido. Usa YYYY-MM, ejemplo: 2025-04',
            ], 422);
        }

        if ($period === now()->format('Y-m')) {
            return $this->mostRated();
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
