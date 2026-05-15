<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\ArtistApplicationStatusEnum;
use App\Models\ArtistApplication;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ArtistApplicationController
{
    public function index(): JsonResponse
    {
        return response()->json(ArtistApplication::all());
    }

    public function store(Request $request): JsonResponse
    {
        try {
            abort_if($this->checkIfUserHasPendingOrAcceptedApplication(), 409, 'Error: ya tienes una solicitud de artista pendiente o aceptada.');

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
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al enviar la solicitud de artista.');
        } catch (Exception $e) {
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error: no se ha podido enviar la solicitud de artista.');
        }
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
