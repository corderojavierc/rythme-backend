<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $posts = Comment::with(['post', 'user'])
            ->withExists(['likes as is_liked' => function (Builder $query): void {
                $query->where('user_id', auth()->id());
            }])
            ->latest()
            ->paginate(120);

        return CommentResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'text' => 'required|string',
        ]);

        $comment = Comment::create([
            'post_id' => $data['post_id'],
            'user_id' => auth()->id(),
            'text' => $data['text'],
            'count_likes' => 0,
        ]);

        return response()->json(true, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            return response()->json($comment, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Comment not found',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            if ($comment->user_id !== auth()->id()) {
                return response()->json([
                    'message' => 'You are not authorized to delete this comment',
                ], 403);
            }
            $comment->delete();

            return response()->json(true, 200);
        } catch (\Exception $e) {
            return response()->json(false, 404);
        }
    }
}
