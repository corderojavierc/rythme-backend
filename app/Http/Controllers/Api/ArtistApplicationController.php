<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\ArtistApplicationStatusEnum;
use App\Models\ArtistApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class ArtistApplicationController
{
    public function index(): Collection
    {
        return ArtistApplication::all();
    }

    public function store(Request $request): JsonResponse
    {
        if ($this->checkIfUserHasPendingOrAcceptedApplication()) {
            return response()->json([
                'error' => 'Ya tienes una aplicación pendiente o aceptada.',
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

        $application = ArtistApplication::query()->create([
            'user_id' => Auth::id(),
            'status' => ArtistApplicationStatusEnum::SENT,
            ...$data,
        ]);

        return response()->json($application, 201);
    }

    public function hasApplication(): JsonResponse
    {
        return response()->json([
            'has_application' => $this->checkIfUserHasPendingOrAcceptedApplication(),
        ]);
    }

    private function checkIfUserHasPendingOrAcceptedApplication(): bool
    {
        return ArtistApplication::query()->where('user_id', Auth::id())
            ->whereIn('status', [
                ArtistApplicationStatusEnum::SENT,
                ArtistApplicationStatusEnum::ACCEPTED,
            ])
            ->exists();
    }
}
