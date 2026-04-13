<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\LikeResource;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

final class LikeController
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $likes = Like::query()->with('user', 'likeable')->where('user_id', $id)->get();
        return LikeResource::collection($likes);
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
     {
         $request->validate([
             'user_id' => ['required', 'exists:users,id'],
             'likeable_type' => ['required', 'string'],
             'likeable_id' => ['required', 'string'],
         ]);

         $like = Like::query()->create([
             'user_id' => $request->user_id,
             'likeable_type' => $request->likeable_type,
             'likeable_id' => $request->likeable_id,
         ]);

         if ($request->likeable_type === 'App\Models\Post') {
             $post = Post::query()->find($request->likeable_id);

             if ($post) {
                 $post->increment('count_likes');
             }
         }

         if ($request->likeable_type === 'App\Models\Comment') {
             $comment = Comment::query()->find($request->likeable_id);

             if ($comment) {
                 $comment->increment('count_likes');
             }
         }

         return response()->json(true, 201);
     }

    /**
     * Update the specified resource in storage.
     */
    public function update(): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'likeable_type' => ['required', 'string'],
            'likeable_id' => ['required', 'string'],
        ]);

        Like::query()->where([
            'user_id' => $request->user_id,
            'likeable_type' => $request->likeable_type,
            'likeable_id' => $request->likeable_id,
        ])->delete();

        return response()->json(true, 204);
    }
}
