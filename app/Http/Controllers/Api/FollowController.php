<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\FollowResource;
use App\Models\Follow;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class FollowController
{
    public function index(string $id): AnonymousResourceCollection
    {
        $follows = Follow::query()
            ->with(['follower', 'followed'])
            ->where('follower_id', $id)
            ->get();

        return FollowResource::collection($follows);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            /** @var array{follower_id: string, followed_id: string} $validated */
            $validated = $request->validate([
                'follower_id' => ['required', 'exists:users,id'],
                'followed_id' => ['required', 'exists:users,id'],
            ]);

            if ($validated['follower_id'] === $validated['followed_id']) {
                throw ValidationException::withMessages([
                    'follower_id' => 'Error: no puedes seguirte a ti mismo.',
                ]);
            }

            abort_if(Follow::query()->where($validated)->exists(), 409, 'Error: ya sigues a este usuario.');

            Follow::query()->create($validated);

            return response()->json([
                'message' => 'Usuario seguido correctamente.',
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al seguir al usuario.');
        } catch (Exception $e) {
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error: no se ha podido seguir al usuario.');
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'follower_id' => ['required', 'exists:users,id'],
                'followed_id' => ['required', 'exists:users,id'],
            ]);

            Follow::query()->where($validated)->get()->each->delete();

            return response()->json([
                'message' => 'Has dejado de seguir al usuario correctamente.',
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al dejar de seguir al usuario.');
        } catch (Exception $e) {
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error: no se ha podido dejar de seguir al usuario.');
        }
    }
}
