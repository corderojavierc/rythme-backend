<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ArtistApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class ArtistApplicationController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return ArtistApplication::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): ArtistApplication
    {
        $userId = auth()->id();
        $data = $request->validate([
            'type' => ['required', 'string'],
            'followers' => ['nullable', 'integer'],
            'listeners' => ['nullable', 'integer'],
            'youtube' => ['nullable', 'string'],
            'tiktok' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string'],
            'spotify' => ['nullable', 'string'],
            'twitch' => ['nullable', 'string'],
            'description' => ['string'],
        ]);

        return ArtistApplication::query()->create([
            'user_id' => $userId,
            ...$data,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(): void
    {
        //
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
    public function destroy(): void
    {
        //
    }
}
