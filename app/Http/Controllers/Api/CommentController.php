<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Gestiona los comentarios en posts
final class CommentController
{
    // Lista todos los comentarios paginados
    public function index(): AnonymousResourceCollection
    {
        $comments = Comment::with(['post', 'user'])
            ->withExists(['likes as is_liked' => function (Builder $query): void {
                $query->where('user_id', Auth::id());
            }])
            ->latest()
            ->paginate(20);

        return CommentResource::collection($comments);
    }

    // Crea un comentario en un post
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'post_id' => ['required', 'exists:posts,id'],
                'text' => ['required', 'string'],
            ]);

            Comment::query()->create([
                'post_id' => $data['post_id'],
                'user_id' => Auth::id(),
                'text' => $data['text'],
                'count_likes' => 0,
            ]);

            return response()->json([
                'message' => 'Comentario creado correctamente.',
            ], 201);
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException) {
            abort(500, 'Error de base de datos al crear el comentario.');
        } catch (Exception) {
            abort(500, 'Error: no se ha podido crear el comentario.');
        }
    }

    // Devuelve todos los comentarios de un post concreto (el ID es el del post)
    public function show(string $id): AnonymousResourceCollection
    {
        try {
            $post = Post::query()->findOrFail($id);

            $comments = $post->comments()
                ->with(['user'])
                ->withExists(['likes as is_liked' => function (Builder $query): void {
                    $query->where('user_id', Auth::id());
                }])
                ->latest()
                ->paginate(10);

            return CommentResource::collection($comments);
        } catch (ModelNotFoundException) {
            abort(404, 'Error: la publicación no ha sido encontrada.');
        } catch (Exception) {
            abort(500, 'Error al obtener los comentarios.');
        }
    }

    public function update(): JsonResponse
    {
        return response()->json([]);
    }

    // Elimina un comentario; solo lo puede hacer su autor
    public function destroy(string $id): JsonResponse
    {
        try {
            /** @var Comment $comment */
            $comment = Comment::query()->findOrFail($id);

            abort_if($comment->user_id !== Auth::id(), 403, 'Error: no tienes permisos para eliminar este comentario.');

            $comment->delete();

            return response()->json([
                'message' => 'Comentario eliminado correctamente.',
            ]);
        } catch (ModelNotFoundException) {
            abort(404, 'Error: el comentario no ha sido encontrado.');
        } catch (Exception $e) {
            throw_if($e instanceof HttpException, $e);

            abort(500, 'Error al eliminar el comentario.');
        }
    }
}
