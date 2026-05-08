<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

final class CommentController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $posts = Comment::with(['post', 'user'])
            ->withExists(['likes as is_liked' => function (Builder $query): void {
                $query->where('user_id', Auth::id());
            }])
            ->latest()
            ->paginate(20);

        return CommentResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
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

        return response()->json(true, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): AnonymousResourceCollection|JsonResponse
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
        } catch (Exception) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(): JsonResponse
    {
        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            /** @var Comment $comment */
            $comment = Comment::query()->findOrFail($id);
            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'You are not authorized to delete this comment',
                ], 403);
            }

            $comment->delete();

            return response()->json(true, 200);
        } catch (Exception) {
            return response()->json(false, 404);
        }
    }
}
