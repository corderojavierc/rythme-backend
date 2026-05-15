<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\LikeResource;
use App\Models\Like;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class LikeController
{
    public function index(string $id): AnonymousResourceCollection
    {
        $likes = Like::query()
            ->with(['user', 'likeable'])
            ->where('user_id', $id)
            ->get();

        return LikeResource::collection($likes);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'likeable_type' => ['required', 'string'],
                'likeable_id' => ['required', 'string'],
            ]);

            abort_if(Like::query()->where($validated)->exists(), 409, 'Error: ya has dado like a este elemento.');

            Like::query()->create($validated);

            return response()->json([
                'message' => 'Like añadido correctamente.',
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al añadir el like.');
        } catch (Exception $e) {
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error: no se ha podido añadir el like.');
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => ['required', 'exists:users,id'],
                'likeable_type' => ['required', 'string'],
                'likeable_id' => ['required', 'string'],
            ]);

            $like = Like::query()->where($validated)->first();

            abort_unless($like instanceof Like, 404, 'Error: el like no ha sido encontrado.');

            $like->delete();

            return response()->json([
                'message' => 'Like eliminado correctamente.',
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (ModelNotFoundException) {
            abort(404, 'Error: el like no ha sido encontrado.');
        } catch (QueryException) {
            abort(500, 'Error de base de datos al eliminar el like.');
        } catch (Exception $e) {
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error: no se ha podido eliminar el like.');
        }
    }
}
