<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ArtistApplicationStatusEnum;
use App\Models\ArtistApplication;
use App\Services\SpotifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class ArtistApplicationController
{
    public function index(): Collection
    {
        return ArtistApplication::all();
    }

    public function store(Request $request): JsonResponse
    {
        if ($this->checkIfUserHasPendingApplication()) {
            return response()->json([
                'error' => 'Ya tienes una aplicación pendiente.',
            ], 400);
        }

        $data = $request->validate([
            'type' => ['required', 'string'],
            'followers' => ['nullable', 'integer'],
            'listeners' => ['nullable', 'integer'],
            'youtube' => ['nullable', 'string'],
            'tiktok' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string'],
            'spotify' => ['nullable', 'string'],
            'twitch' => ['nullable', 'string'],
            'description' => ['required', 'string'],
        ]);

        if (! empty($data['spotify'])) {
            $resolvedName = SpotifyService::getArtistName($data['spotify']);
            if ($resolvedName) {
                $data['spotify'] = $resolvedName;
            }
        }

        $application = ArtistApplication::query()->create([
            'user_id' => auth()->id(),
            'status' => ArtistApplicationStatusEnum::SENT,
            ...$data,
        ]);

        return response()->json($application, 201);
    }

    public function hasApplication(): JsonResponse
    {
        return response()->json([
            'has_application' => $this->checkIfUserHasPendingApplication(),
        ]);
    }

    private function checkIfUserHasPendingApplication(): bool
    {
        return ArtistApplication::query()->where('user_id', auth()->id())
            ->where('status', ArtistApplicationStatusEnum::SENT)
            ->exists();
    }
}
