<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Http\Resources\FollowResource;

use Illuminate\Http\Request;

final class FollowController
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $follows = Follow::query()->with('follower', 'followed')->where('follower_id', $id)->get();
        return FollowResource::collection($follows);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'follower_id' => ['required', 'exists:users,id'],
            'followed_id' => ['required', 'exists:users,id'],
        ]);

        Follow::query()->create([
            'follower_id' => $request->follower_id,
            'followed_id' => $request->followed_id,
        ]);

        if (Follow::query()->where([
            'follower_id' => $request->follower_id,
            'followed_id' => $request->followed_id,
        ])->exists()) {
            return response()->json(true, 201);
        } else {
            return response()->json(false, 400);
        }
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
            'follower_id' => ['required', 'exists:users,id'],
            'followed_id' => ['required', 'exists:users,id'],
        ]);

        Follow::query()->where([
            'follower_id' => $request->follower_id,
            'followed_id' => $request->followed_id,
        ])->delete();

        return response()->json(true, 204);
    }
}
